<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Http\Resources\UserSelectedPlanResource;
use App\Trait\Likeable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
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

    protected $appends = [
        'province',
        'golds',
        'coins',
        'selected_plan',
        'coin_wallet',
        'gold_wallet',
        'avatar',
        'national_code',
        'tel_number',
        'location',
        'work_hours',
        'work_on_holidays',
        'is_closed',
        'is_all_day_open',
        'social_media',
        'address',
        'is_bookmarked',
        'has_blue_tick',
        'bio',
        'is_artist',
        'is_artist_agreed',
        'documents',
        'artist_banner',
        'is_artist_profile_completed',
        'services_count',
        'portfolios_count',
        'licenses',
        'rating',
        'is_bookmarked',
    ];

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

    public function getIsBookmarkedAttribute()
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

    public function getProvinceAttribute()
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

    public function getCoinWalletAttribute()
    {
        return $this->wallets()->where('type', 'coin')->firstOrCreate(['type' => 'coin']);
    }

    public function getCoinsAttribute()
    {
        return $this->coin_wallet->amount;
    }

    public function getGoldWalletAttribute()
    {
        return $this->wallets()->where('type', 'gold')->firstOrCreate(['type' => 'gold']);
    }

    public function getGoldsAttribute()
    {
        return $this->gold_wallet->amount;
    }

    public function getAvatarAttribute()
    {
        return $this->getMeta('avatar');
    }

    public function getBannersAttribute()
    {
        return $this->getMeta('banners');
    }

    public function getLicensesAttribute()
    {
        return $this->getMeta('licenses');
    }

    public function getRatingAttribute()
    {
        return 4.5;
    }

    public function getNationalCodeAttribute()
    {
        return $this->getMeta('national_code');
    }

    public function getTelNumberAttribute()
    {
        return $this->getMeta('tel_number');
    }

    public function getAddressAttribute()
    {
        return $this->getMeta('address');
    }

    public function getIsArtistAgreedAttribute()
    {
        return $this->getMeta('is_artist_agreed');
    }

    public function getBioAttribute()
    {
        return $this->getMeta('bio');
    }

    public function getWorkHoursAttribute()
    {
        return $this->getMeta('work_hours');
    }

    public function getSocialMediaAttribute()
    {
        return $this->getMeta('social_media');
    }

    public function getWorkOnHolidaysAttribute()
    {
        return $this->getMeta('work_on_holidays');
    }

    public function getIsClosedAttribute()
    {
        return $this->getMeta('is_closed');
    }

    public function getIsAllDayOpenAttribute()
    {
        return $this->getMeta('is_all_day_open');
    }

    public function getLocationAttribute()
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

    public function getSelectedPlanAttribute()
    {
        $last = $this->plans()
            ->where(function (Builder $q) {

            })
            ->orderByDesc('created_at')
            ->first();
        if ($last) {
            return new UserSelectedPlanResource($last);
        }
        return new UserSelectedPlanResource($this->plans()->orderBy('created_at')->first());
    }

    public function getIsArtistAttribute()
    {
        return $this->hasRole('artist');
    }

    public function getIsArtistProfileCompletedAttribute()
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
        if ($isCompleted) {
            $metas = $this->metas;
            $metas = $metas->map(function($i) {return $i->value ? $i->key : '';})->filter(function ($i) {return $i != '';})->toArray();
//            $isCompleted = in_array('national_code', $metas) &&
//                in_array('address', $metas) &&
//                in_array('tel_number', $metas) &&
//                in_array('work_hours', $metas) &&
//                in_array('bio', $metas);
        }
        if ($isCompleted) {
            if (!$this->hasRole('artist')) {
                $this->assignRole('artist');
            }
        }
        return $isCompleted;
    }

    public function getArtistBannerAttribute()
    {
        return $this->getMeta('artist_banner');
    }

    public function getDocumentsAttribute()
    {
        return $this->getMeta('documents');
    }

    public function getHasBlueTickAttribute()
    {
        return $this->selected_plan->plan->has_blue_tick;
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function getPortfoliosCountAttribute()
    {
        return $this->portfolios()->count();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function getServicesCountAttribute()
    {
        return $this->services()->count();
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
}
