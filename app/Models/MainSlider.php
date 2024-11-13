<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'main_title',
        'sub_title',
        'description',
        'link_url',
        'link_title',
    ];
}
