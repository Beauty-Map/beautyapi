<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSelectPlanRequest;
use App\Http\Resources\UserPlanResource;
use App\Interfaces\UserPlanInterface;
use App\Models\Plan;
use Carbon\Carbon;

class UserPlanController extends Controller
{
    public UserPlanInterface $userPlanRepository;

    public function __construct(
        UserPlanInterface $userPlanRepository,
    )
    {
        $this->userPlanRepository = $userPlanRepository;
    }

    public function index()
    {
        $auth = $this->getAuth();
        $filter = [
            'user_id' => $auth->id,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $plans = $this->userPlanRepository->findByPaginate($filter, $page, $limit, 'created_at', 'desc');
        } else {
            $plans = $this->userPlanRepository->findByPaginate($filter, 'created_at', 'desc');
        }
        return UserPlanResource::collection($plans);
    }

    public function selectPlan(UserSelectPlanRequest $request)
    {
        $auth = $this->getAuth();
        /** @var Plan $plan */
        $plan = Plan::query()->findOrFail($request->plan_id);
        if ($plan->coins > $auth->getCoins()) {
            return $this->createError('coins', 'موجودی شما برای خرید این پلن کافی نیست!', 422);
        }
        $w = $auth->getCoinWallet();
        $now = Carbon::now();
        $auth->plans()->create([
            'plan_id' => $plan->id,
            'status' => 'payed',
            'start_date' => $now,
            'end_date' => $now->addMonths(1),
            'duration' => 30,
            'amount' => $plan->coins,
        ]);
        $w->update([
            'amount' => $w->amount - $plan->coins,
        ]);
        return true;
    }

}
