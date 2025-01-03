<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'coins',
        'portfolio_count',
        'laddering_count',
        'star_count',
        'has_blue_tick',
        'image_upload_count',
        'has_discount',
        'discount_number',
        'color',
    ];

    protected $casts = [
        'has_blue_tick' => 'boolean',
        'has_discount' => 'boolean',
    ];
}
