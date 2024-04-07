<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Events\SendRegisterOtpEvent;
use App\Helpers\Helper;
use App\Http\Requests\CheckRegisterOtpCodeRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SetRegisterPasswordRequest;
use App\Http\Resources\UserLoginResource;
use App\Interfaces\OtpInterface;
use App\Interfaces\UserInterface;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected UserInterface $userRepository;
    protected OtpInterface $otpRepository;

    public function __construct(
        UserInterface $userRepository,
        OtpInterface $otpRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
    }

    public function register(RegisterUserRequest $request)
    {
        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy(['phone_number' => $request['phone_number']]);
        if ($user) {
            return $this->createError('USER_REGISTERED_BEFORE_ERROR', Constants::USER_REGISTERED_BEFORE_ERROR,403);
        }
        $user = $this->userRepository->create($request->only([
            'phone_number'
        ]));
        event(new SendRegisterOtpEvent($user));
        return $this->createCustomResponse('', 201);
    }

    public function checkOtpCode(CheckRegisterOtpCodeRequest $request)
    {
        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        $otp = $this->otpRepository->findOneBy([
            'phone_number' => $request['phone_number'],
            'type' => Otp::REGISTER_OTP_TYPE,
            'code' => $request['code'],
        ]);
        if (!$otp) {
            return $this->createError('INVALID_OTP_CODE_ERROR', Constants::INVALID_OTP_CODE_ERROR,404);
        }
        $user = $this->userRepository->findOneBy(['phone_number' => $request['phone_number']]);
        $this->userRepository->update([
            'remember_token' => Helper::randomCode(10),
        ], $user->id);
        $otp->delete();
        return $this->createCustomResponse($user->remember_token);
    }

    public function setPassword(SetRegisterPasswordRequest $request)
    {
        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy([
            'phone_number' => $request['phone_number'],
            'remember_token' => $request['remember_token'],
        ]);
        if (!$user) {
            return $this->createError('USER_NOT_FOUND_ERROR', Constants::USER_NOT_FOUND_ERROR,404);
        }
        $this->userRepository->update([
            'password' => Hash::make($request['password']),
            'remember_token' => null,
        ], $user->id);
        $token = $user->createToken(env('APP_NAME'))->plainTextToken;
        return new UserLoginResource($user, $token);
    }

    public function login(LoginUserRequest $request)
    {
        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
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
