<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSelectPlanRequest;
use App\Http\Resources\UserPlanResource;
use App\Interfaces\UserPlanInterface;

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

    public function setPlan(UserSelectPlanRequest $request)
    {
        $auth = $this->getAuth();

    }

}
