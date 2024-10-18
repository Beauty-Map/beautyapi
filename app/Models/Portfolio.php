<?php

namespace App\Models;

use App\Trait\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory, Likeable;

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
        'status',
    ];

    protected $casts = [
        'images' => 'json',
        'has_tel' => 'boolean',
        'has_phone_number' => 'boolean',
    ];

    protected $appends = [
        'work_hours',
        'images_list',
        'is_bookmarked',
    ];

    public function getIsBookmarkedAttribute()
    {
        if (auth('api')->user()) {
            $auth = auth('api')->user();
            return $this->isLikedBy($auth->id);
        }
        return false;
    }

    public function getWorkHoursAttribute()
    {
        return $this->user->getMeta('work_hours');
    }

    public function getImagesListAttribute()
    {
        return $this->images;
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
