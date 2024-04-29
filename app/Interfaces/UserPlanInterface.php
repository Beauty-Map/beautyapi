<?php


namespace App\Interfaces;

use Carbon\Carbon;

/**
 * Interface UserPlanInterface
 * @package App\Interfaces
 */
interface UserPlanInterface extends BaseInterface
{
    public function setPlan(int $userId, int $planId, Carbon $startDate = null, Carbon $endDate = null, int $duration = -1, int $amount = 0);
}
