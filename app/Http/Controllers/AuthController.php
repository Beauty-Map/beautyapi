<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserLoginResource;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected UserInterface $userRepository;

    public function __construct(
        UserInterface $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterUserRequest $request)
    {
        $request['password'] = Hash::make($request['password']);
        $request['phone_number'] = $this->normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy(['phone_number' => $request['phone_number']]);
        if ($user) {
            return $this->createError('USER_REGISTERED_BEFORE_ERROR', Constants::USER_REGISTERED_BEFORE_ERROR,403);
        }
        $user = $this->userRepository->create($request->all());
        $token = $user->createToken(env('APP_NAME'))->plainTextToken;
        return new UserLoginResource($user, $token);
    }

    public function login(LoginUserRequest $request)
    {
        $request['phone_number'] = $this->normalizePhoneNumber($request['phone_number']);
        if(Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])){
            /** @var User $user */
            $user = Auth::user();
            $token =  $user->createToken(env('APP_NAME'))->plainTextToken;
            return new UserLoginResource($user, $token);
        }
        else{
            return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
        }
    }
}
