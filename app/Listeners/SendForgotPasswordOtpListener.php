<?php

namespace App\Listeners;

use App\Events\SendForgotPasswordOtpEvent;
use App\Helpers\Helper;
use App\Mail\SendRegisterVerifyCodeEmail;
use App\Models\Otp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendForgotPasswordOtpListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendForgotPasswordOtpEvent $event): void
    {
        $user = $event->user;
        $code = Helper::randomCode(6, 'number');
        Otp::query()->updateOrCreate([
            'phone_number' => $user->email,
            'code' => $code,
            'type' => Otp::FORGOT_PASSWORD_OTP_TYPE
        ]);
        Mail::to($user->email)->send(new SendRegisterVerifyCodeEmail($code));
        //TODO: send sms
    }
}
