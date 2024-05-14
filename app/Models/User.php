<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'password',
        'is_active',
        'city_id',
        'birth_date',
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
        ];
    }

    protected $with = [
        'metas',
    ];

    protected $appends = [
        'province',
        'golds',
        'coins',
        'selected_plan',
        'coin_wallet',
        'gold_wallet',
        'avatar',
    ];

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
            return $last;
        }
        return $this->plans()->orderBy('created_at')->first();
    }
}
