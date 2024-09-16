<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserSimpleResource;
use App\Interfaces\UserInterface;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    protected UserInterface $userRepository;

    public function __construct(
        UserInterface $userRepository,
    )
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $filter = [
            'referrer_code' => \request()->input('ref', 'none'),
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $users = $this->userRepository->referredByPagination($filter, $page, $limit);
        } else {
            $users = $this->userRepository->referredBy($filter);
        }
        return UserSimpleResource::collection($users);
    }

}
