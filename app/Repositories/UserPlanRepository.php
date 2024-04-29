<?php


namespace App\Repositories;

use App\Interfaces\UserPlanInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserPlanRepository
 *
 * @package \App\Repositories
 */
class UserPlanRepository extends BaseRepository implements UserPlanInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function setPlan(int $userId, int $planId, Carbon $startDate = null, Carbon $endDate = null, int $duration = -1, int $amount = 0)
    {
        return $this->model->create([
            'plan_id' => $planId,
            'user_id' => $userId,
            'status' => 'verified',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration' => $duration,
            'amount' => $amount,
        ]);
    }
}
