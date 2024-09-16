<?php

namespace App\Listeners;

use App\Events\SendRegisterOtpEvent;
use App\Helpers\Helper;
use App\Helpers\LimooSMS;
use App\Mail\SendRegisterVerifyCodeEmail;
use App\Models\Otp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendRegisterOtpListener implements ShouldQueue
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
        $code = Helper::randomCode(6, 'number');
        Otp::query()->updateOrCreate([
            'phone_number' => $user->email,
            'code' => $code,
            'type' => Otp::REGISTER_OTP_TYPE
        ]);
        Mail::to($user->email)->send(new SendRegisterVerifyCodeEmail($code));
        //TODO: send sms
//        (new LimooSMS())->sendOtpCode($user->phone_number);
    }
}
