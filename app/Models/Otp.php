<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    const REGISTER_OTP_TYPE = 'register';
    const FORGOT_PASSWORD_OTP_TYPE = 'forgot-password';

    protected $fillable = [
        'phone_number',
        'code',
        'type',
    ];
}
