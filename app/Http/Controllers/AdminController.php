<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserSimpleResource;
use App\Interfaces\IntroInterface;
use App\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public UserInterface $userRepository;
    public IntroInterface $introRepository;

    public function __construct(
        UserInterface $userRepository,
        IntroInterface $introRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->introRepository = $introRepository;
    }

    public function indexUsers()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $users = $this->userRepository->findByPaginate([], $page, $limit);
        } else {
            $users = $this->userRepository->findBy([]);
        }
        return UserSimpleResource::collection($users);
    }

    public function showUsers(int $id)
    {
        return $this->userRepository->findOneOrFail($id);
    }

    public function destroyUsers(int $id)
    {
        return $this->userRepository->delete($id);
    }

    public function indexRoles()
    {
        return Role::all();
    }
}
