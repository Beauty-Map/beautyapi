<?php

namespace App\Listeners;

use App\Events\SendRegisterOtpEvent;
use App\Helpers\Helper;
use App\Helpers\LimooSMS;
use App\Models\Otp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRegisterOtpListener
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
    public function handle(SendRegisterOtpEvent $event): void
    {
        $user = $event->user;
        $code = Helper::randomCode(4);
        Otp::query()->updateOrCreate([
            'phone_number' => $user->phone_number,
            'code' => $code,
            'type' => Otp::REGISTER_OTP_TYPE
        ]);
        //TODO: send sms
        (new LimooSMS())->sendOtpCode($user->phone_number);
    }
}
