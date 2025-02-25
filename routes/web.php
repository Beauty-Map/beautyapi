<?php

use App\Helpers\Helper;
use App\Mail\SendRegisterVerifyCodeEmail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $email = 'saeedhpro@gmail.com';
    $code = Helper::randomCode(6, 'number');
    Otp::query()->updateOrCreate([
        'phone_number' => $email,
        'code' => $code,
        'type' => Otp::REGISTER_OTP_TYPE
    ]);
    return Mail::to($email)->send(new SendRegisterVerifyCodeEmail($code))->toString();
});
