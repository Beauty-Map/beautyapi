<?php

namespace App\Models;

use App\Helpers\Helper;
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

    public function getCoinsAttribute()
    {
        $wallet = $this->wallets()->where('type', 'coin')->firstOrCreate(['type' => 'coin']);
        return $wallet->amount;
    }

    public function getGoldsAttribute()
    {
        $wallet = $this->wallets()->where('type', 'gold')->firstOrCreate(['type' => 'gold']);
        return $wallet->amount;
    }
}
