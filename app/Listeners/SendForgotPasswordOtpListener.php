<?php

namespace App\Listeners;

use App\Events\SendForgotPasswordOtpEvent;
use App\Helpers\Helper;
use App\Models\Otp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendForgotPasswordOtpListener
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
        $code = Helper::randomCode(4);
        Otp::query()->updateOrCreate([
            'phone_number' => $user->phone_number,
            'code' => $code,
            'type' => Otp::FORGOT_PASSWORD_OTP_TYPE
        ]);
        //TODO: send sms
    }
}
