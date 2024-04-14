<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'showing_phone_number',
        'images',
    ];

    protected $casts = [
        'images' => 'json'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
