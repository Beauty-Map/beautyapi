<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Http\Resources\UserSelectedPlanResource;
use App\Trait\Likeable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Morilog\Jalali\Jalalian;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions, HasApiTokens, Likeable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'password',
        'is_active',
        'city_id',
        'birth_date',
        'lat',
        'lng',
        'remember_token',
        'referrer_code',
        'referral_code',
        'subscription_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_closed' => 'boolean',
            'is_all_day_open' => 'boolean',
        ];
    }

    public function addView()
    {
        $view = $this->getMeta('view');
        if (!$view) {
            $view = 0;
        }
        $this->setMeta('view', $view + 1);
    }

    public function getView()
    {
        $view = $this->getMeta('view');
        if (!$view) {
            $view = 0;
        }
        return $view;

    }

    public function levelOneReferrals()
    {
        return $this->hasMany(User::class, 'referrer_code', 'referral_code');
    }

    public function levelTwoReferrals()
    {
        return $this->levelOneReferrals()
            ->join('users AS u2', 'users.referral_code', '=', 'u2.referrer_code')
            ->select('u2.*');
    }

    public function levelThreeReferrals()
    {
        return $this->levelTwoReferrals()
            ->join('users AS u3', 'users.referral_code', '=', 'u3.referrer_code')
            ->select('u3.*');
    }

    public function levelFourReferrals()
    {
        return $this->levelThreeReferrals()
            ->join('users AS u4', 'users.referral_code', '=', 'u4.referrer_code')
            ->select('u4.*');
    }

    public function getMonthPortfolios()
    {
        $startOfMonth = Jalalian::now()->getFirstDayOfMonth()->toCarbon();
        $endOfMonth = Jalalian::now()->getEndDayOfMonth()->toCarbon();
        return $this->portfolios()->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
    }

    public function getYearPortfolios()
    {
        $startOfYear = Jalalian::now()->getFirstDayOfYear()->toCarbon();
        $endOfYear = Jalalian::now()->getEndDayOfYear()->toCarbon();
        return $this->portfolios()->whereBetween('created_at', [$startOfYear, $endOfYear]);
    }

//    protected $appends = [
//        'province',
//        'golds',
//        'coins',
//        'selected_plan',
//        'coin_wallet',
//        'gold_wallet',
//        'avatar',
//        'national_code',
//        'tel_number',
//        'location',
//        'work_hours',
//        'work_on_holidays',
//        'is_closed',
//        'is_all_day_open',
//        'social_media',
//        'address',
//        'is_bookmarked',
//        'has_blue_tick',
//        'bio',
//        'is_artist',
//        'is_artist_agreed',
//        'documents',
//        'artist_banner',
//        'is_artist_profile_completed',
//        'services_count',
//        'portfolios_count',
//        'licenses',
//        'rating',
//        'is_bookmarked',
//        'wallet_address',
//        'pending_bonuses',
//        'payed_bonuses',
//        'pending_income',
//    ];

    protected static function booted()
    {
        static::created(function ($user) {
            do {
                $referralCode = Helper::randomCode(6, 'number');
            } while (User::where('referral_code', $referralCode)->exists());
            $user->referral_code = $referralCode;
            $user->save();
        });
    }

    public function isBookmarked()
    {
        if (auth('api')->user()) {
            $auth = auth('api')->user();
            return $this->isLikedBy($auth->id);
        }
        return false;
    }

    public function metas()
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function getProvince()
    {
        return $this->city ? $this->city->province : null;
    }

    public function getMeta($key = '')
    {
        return Helper::getMeta($this, $key);
    }

    public function setMeta($key = '', $value = '')
    {
        return Helper::setMeta($this, $key, $value);
    }

    public function wallets()
    {
        return $this->morphMany(Wallet::class, 'walletable');
    }

    public function getCoinWallet()
    {
        return $this->wallets()->where('type', 'coin')->firstOrCreate(['type' => 'coin']);
    }

    public function getIncome()
    {
        return floatval($this->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PENDING)
            ->sum('amount'));
    }

    public function getWithdraw()
    {
        return floatval($this->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PAYED)
            ->sum('amount'));
    }

    public function getAllIncome()
    {
        return floatval($this->bonusTransactions()
            ->sum('amount'));
    }

    public function getOldWithdraws()
    {
        if ($this->getAllIncome() == 0) {
            return 0;
        }
        return $this->getWithdraw() / $this->getAllIncome();
    }

    public function getCoins()
    {
        return $this->getCoinWallet()->amount;
    }

    public function getPendingBonuses()
    {
        return $this->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PENDING);
    }

    public function getPayedBonuses()
    {
        return $this->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PAYED);
    }

    public function getPendingIncomeAttribute()
    {
        return $this->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PENDING)
            ->sum('amount');
    }

    public function getGoldWallet()
    {
        return $this->wallets()->where('type', 'gold')->firstOrCreate(['type' => 'gold']);
    }

    public function getGolds()
    {
        return $this->getGoldWallet()->amount;
    }

    public function getAvatar()
    {
        return $this->getMeta('avatar');
    }

    public function getGender()
    {
        return $this->getMeta('gender');
    }

    public function getTonWalletAddress()
    {
        return $this->getMeta('ton_wallet_address');
    }

    public function setTonWalletAddress(string $address = '')
    {
        return $this->setMeta('ton_wallet_address', $address);
    }

    public function getBanners()
    {
        return $this->getMeta('banners');
    }

    public function getLicenses()
    {
        $res = $this->getMeta('licenses') ?? [];
        return $array = json_decode($res, true);
    }

    public function getRating()
    {
        return 4.5;
    }

    public function getNationalCode()
    {
        return $this->getMeta('national_code');
    }

    public function getPostalCode()
    {
        return $this->getMeta('postal_code');
    }

    public function getEducation()
    {
        return $this->getMeta('education');
    }

    public function getAccountFullName()
    {
        return $this->getMeta('account_full_name');
    }

    public function getAccountNumber()
    {
        return $this->getMeta('account_number');
    }

    public function getSheba()
    {
        return $this->getMeta('sheba');
    }

    public function getBankName()
    {
        return $this->getMeta('bank_name');
    }

    public function getCardNumber()
    {
        return $this->getMeta('card_number');
    }

    public function getTelNumber()
    {
        return $this->getMeta('tel_number');
    }

    public function getAltNumber()
    {
        return $this->getMeta('alt_number');
    }

    public function getAddress()
    {
        return $this->getMeta('address');
    }

    public function getIsArtistAgreed()
    {
        return $this->getMeta('is_artist_agreed');
    }

    public function getIsProfileCompleted()
    {
        return $this->full_name && $this->email && $this->phone_number && $this->birth_date
            && $this->city_id;
    }

    public function getBio()
    {
        return $this->getMeta('bio');
    }

    public function getWorkHours()
    {
        return $this->getMeta('work_hours');
    }

    public function getSocialMedia()
    {
        return $this->getMeta('social_media');
    }

    public function getWorkOnHolidays()
    {
        return $this->getMeta('work_on_holidays');
    }

    public function getWalletAddress()
    {
        return $this->getMeta('wallet_address');
    }

    public function getIsClosed()
    {
        return $this->getMeta('is_closed');
    }

    public function getIsAllDayOpen()
    {
        return $this->getMeta('is_all_day_open');
    }

    public function getLocation()
    {
        if ($this->lat && $this->lng) {
            return ['lat' => $this->lat, 'lng' => $this->lng];
        }
        return null;
    }

    public function plans()
    {
        return $this->hasMany(UserPlan::class);
    }

    public function getSelectedPlan()
    {
        $last = $this->plans()
            ->orderByDesc('created_at')
            ->first();
        if ($last) {
            return new UserSelectedPlanResource($last);
        }
        $plan = Plan::query()->orderBy('id')->first();
        $userPlan = $this->plans()->create([
            'plan_id' => $plan->id,
            'status' => 'payed',
            'start_date' => Carbon::now(),
            'end_date' => null,
            'duration' => -1,
            'amount' => 0,
        ]);
        return new UserSelectedPlanResource($userPlan);
    }

    public function getIsArtist()
    {
        return $this->hasRole('artist');
    }

    public function getIsArtistProfileCompleted()
    {
        $isCompleted = true;
        if (!$this->full_name) {
            $isCompleted = false;
        }
        if (!$this->phone_number) {
            $isCompleted = false;
        }
        if (!$this->city_id) {
            $isCompleted = false;
        }
//        if ($isCompleted) {
//            $metas = $this->metas;
//            $metas = $metas->map(function($i) {return $i->value ? $i->key : '';})->filter(function ($i) {return $i != '';})->toArray();
//            $isCompleted = in_array('national_code', $metas) &&
//                in_array('address', $metas) &&
//                in_array('tel_number', $metas) &&
//                in_array('work_hours', $metas) &&
//                in_array('bio', $metas);
//        }
        if ($isCompleted) {
            if (!$this->hasRole('artist', 'api')) {
                $artist = Role::query()->where(['name' => 'artist', 'guard_name' => 'api'])->first();
                $this->assignRole($artist);
            }
        }
        return $isCompleted;
    }

    public function getArtistBanner()
    {
        return Str::length($this->getMeta('artist_banner')) > 0 ? explode(',', $this->getMeta('artist_banner')): [];
    }

    public function getDocuments()
    {
        return $this->getMeta('documents');
    }

    public function getHasBlueTick()
    {
        return $this->getSelectedPlan()->resource && $this->getSelectedPlan()->plan ? $this->getSelectedPlan()->plan->has_blue_tick : false;
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function getPortfoliosCount()
    {
        return $this->portfolios()->count();
    }

    public function services()
    {
        return $this->hasManyThrough(
            Service::class,
            Portfolio::class,
            'user_id',
            'id',
            'id',
            'service_id'
        );
    }

    public function getServicesCount()
    {
        return $this->services()->pluck('services.id')->unique()->count();
    }

    public function likedItems($modelType = null)
    {
        $query = $this->likes()->with('likeable');

        if ($modelType) {
            $query->where('likeable_type', $modelType);
        }

        return $query->get()->pluck('likeable');
    }

    public function likedItemsByTrait($modelClass)
    {
        return $modelClass::likedByUser($this->id);
    }

    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function bonusTransactions()
    {
        return $this->hasMany(BonusTransaction::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_code', 'referral_code');
    }

    function getAllReferrers()
    {
        $referrers = [];
        $user = $this;

        while ($user && $user->referrer_code) {
            $referrer = User::where('referral_code', $this->referrer_code)->first();
            if ($referrer) {
                $referrers[] = $referrer;
                $user = $referrer;
            } else {
                break;
            }
        }
        return $referrers;
    }

    function distributeCoins($app, $amount) {
        $user = $this;
        $referrer = $user->referrer;
        $bonuses = [];
        $percentages = Setting::first()->toPercentages();

        foreach ($percentages as $level => $percentage) {
            if ($referrer) {
                $bonus = ($amount * $percentage) / 100;
                if ($bonus > 0) {
                    $bt = $referrer->bonusTransactions()->create([
                        'status' => BonusTransaction::STATUS_PENDING,
                        'amount' => $bonus,
                        'referrer_id' => $this->id,
                        'level' => $level,
                        'app' => $app,
                    ]);
                    $bonuses[] = $bt;
                    $w = $referrer->getGoldWallet();
                    $w->update([
                        'amount' => $w->amount + $bonus,
                    ]);
                    $referrer = $referrer->referrer;
                }
            } else {
                break;
            }
        }
        return $bonuses;
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->orderByDesc('start_at')
            ->where('end_at', '>=', Carbon::now())
        ->first();
    }
}
