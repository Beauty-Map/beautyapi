<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Trait\Likeable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'images_list' => 'json',
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
    public function metas()
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function ladders()
    {
        return $this->hasMany(Ladder::class);
    }

    public function isLaddered()
    {
        return $this->ladders()->where('end_at', '>', Carbon::now())->count() > 0;
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

    public function getMeta($key = '')
    {
        return Helper::getMeta($this, $key);
    }

    public function setMeta($key = '', $value = '')
    {
        return Helper::setMeta($this, $key, $value);
    }
}
