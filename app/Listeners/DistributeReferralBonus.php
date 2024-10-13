<?php

namespace App\Listeners;

use App\Events\PurchaseCompleted;
use App\Models\BonusTransaction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DistributeReferralBonus
{
    protected $levels = [
        1 => 10, // 10% for first-level referrer
        2 => 7,  // 7% for second-level referrer
        3 => 4,  // 4% for third-level referrer
        4 => 2,  // 2% for fourth-level referrer
    ];

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
    public function handle(PurchaseCompleted $event): void
    {
        $user = $event->user;
        $coinAmount = $event->coinAmount;

        $currentUser = $user;
        $level = 1;

        while ($level <= 4 && $currentUser->referrer_code) {
            /** @var User $referrer */
            $referrer = User::where('referral_code', $currentUser->referrer_code)->first();
            if (!$referrer) break;

            // Calculate bonus
            $bonusPercentage = $this->levels[$level];
            $bonusAmount = ($coinAmount * $bonusPercentage) / 100;

            // Log the transaction for future reference
            $referrer->bonusTransactions()->create([
                'amount' => $bonusAmount,
                'level' => $level,
                'referrer_id' => $user->id,
                'status' => BonusTransaction::STATUS_PENDING,
            ]);

            // Move up the referral chain
            $currentUser = $referrer;
            $level++;
        }
    }
}
