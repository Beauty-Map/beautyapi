<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory;

    protected $with = [
        'user',
        'service',
    ];

    protected $fillable = [
        'title',
        'description',
        'service_id',
        'user_id',
        'price',
        'discount_price',
        'maintenance',
        'showing_phone_number',
        'images',
        'has_tel',
        'has_phone_number',
    ];

    protected $casts = [
        'images' => 'json',
        'has_tel' => 'boolean',
        'has_phone_number' => 'boolean',
    ];

    protected $appends = [
        'work_hours',
        'images_list',
    ];

    public function getWorkHoursAttribute()
    {
        return $this->user->getMeta('work_hours');
    }

    public function getImagesListAttribute()
    {
        return Str::length($this->images) > 0 ? explode(',', $this->images) : [];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
