<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'body',
        'user_id',
    ];

    protected $casts = [
        'body' => 'array',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
