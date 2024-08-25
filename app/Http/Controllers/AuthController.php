<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Events\SendForgotPasswordOtpEvent;
use App\Events\SendRegisterOtpEvent;
use App\Helpers\Helper;
use App\Helpers\LimooSMS;
use App\Http\Requests\CheckRegisterOtpCodeRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SetRegisterPasswordRequest;
use App\Http\Resources\UserLoginResource;
use App\Interfaces\OtpInterface;
use App\Interfaces\PlanInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\UserPlanInterface;
use App\Models\Otp;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected UserInterface $userRepository;
    protected UserPlanInterface $userPlanRepository;
    protected OtpInterface $otpRepository;
    protected PlanInterface $planRepository;

    public function __construct(
        UserInterface $userRepository,
        OtpInterface $otpRepository,
        PlanInterface $planRepository,
        UserPlanInterface $userPlanRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
        $this->planRepository = $planRepository;
        $this->userPlanRepository = $userPlanRepository;
    }

    public function register(RegisterUserRequest $request)
    {
//        $request['email'] = Helper::normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        if ($user) {
            if ($user->is_active != 0) {
                return $this->createError('USER_REGISTERED_BEFORE_ERROR', Constants::USER_REGISTERED_BEFORE_ERROR,403);
            }
        } else {
            $request['is_active'] = false;
            $request['remember_token'] = Helper::randomCode(10);
            $request['password'] = Hash::make($request['password']);
            /** @var User $user */
            $user = $this->userRepository->create($request->only([
                'email',
                'password',
                'is_active',
                'remember_token',
            ]));
            $plan = Plan::query()->orderBy('id')->first();
            $user->plans()->create([
                'plan_id' => $plan->id,
                'status' => 'payed',
                'start_date' => Carbon::now(),
                'end_date' => null,
                'duration' => -1,
                'amount' => 0,
            ]);
        }
        event(new SendRegisterOtpEvent($user));
        return $this->createCustomResponse($request['remember_token'], 201);
    }

    public function checkOtpCode(CheckRegisterOtpCodeRequest $request)
    {
        return $this->getOtpCodeByType($request);
    }

    public function setPassword(SetRegisterPasswordRequest $request)
    {
//        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy([
            'email' => $request['email'],
            'remember_token' => $request['remember_token'],
        ]);
        if (!$user) {
            return $this->createError('USER_NOT_FOUND_ERROR', Constants::USER_NOT_FOUND_ERROR,404);
        }
        $this->userRepository->update([
            'password' => Hash::make($request['password']),
            'remember_token' => null,
            'is_active' => true,
        ], $user->id);
        $token = $user->createToken(env('APP_NAME'))->plainTextToken;
        return new UserLoginResource($user, $token);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true,
        ];
        if(Auth::attempt($credentials)){
            /** @var User $user */
            $user = Auth::user();
            $token =  $user->createToken(env('APP_NAME'))->plainTextToken;
            return new UserLoginResource($user, $token);
        }
        else{
            return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
        }
    }

    public function adminLogin(LoginUserRequest $request)
    {
        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        if(Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])){
            /** @var User $user */
            $user = Auth::user();
            if (!$user->hasAnyRole(['admin', 'super-admin'])) {
                Auth::logout();
                return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
            }
            $token =  $user->createToken(env('APP_NAME'))->plainTextToken;
            return new UserLoginResource($user, $token);
        }
        else{
            return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
        }
    }

    public function own()
    {
        return $this->getAuth();
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
//        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        if (!$user) {
            return $this->createError('USER_NOT_FOUND_ERROR', Constants::USER_NOT_FOUND_ERROR,404);
        }
        event(new SendForgotPasswordOtpEvent($user));
        return $this->createCustomResponse('', 201);
    }

    public function checkForgotPasswordOtpCode(CheckRegisterOtpCodeRequest $request)
    {
        return $this->getOtpCodeByType($request, Otp::FORGOT_PASSWORD_OTP_TYPE);
    }

    private function getOtpCodeByType(FormRequest $request, string $type = Otp::REGISTER_OTP_TYPE)
    {
//        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
//        $validated = (new LimooSMS())->checkOtpCode($request['email'], $request['code']);
        $email = $request['email'];
        $code = $request['code'];
        $validated = Otp::query()
            ->where('phone_number', $email)
            ->where('type', $type)
            ->where('code', $code)
            ->where('created_at', '>=', Carbon::now()->subMinute())
            ->first();
        if (!$validated) {
            return $this->createError('INVALID_OTP_CODE_ERROR', Constants::INVALID_OTP_CODE_ERROR,404);
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        $token = Helper::randomCode(10);
        $this->userRepository->update([
            'remember_token' => $token,
            'is_active' => 1
        ], $user->id);
        $plan = $this->planRepository->find(1);
        $this->userPlanRepository->setPlan($user->id, $plan->id, null, null, -1, $plan->coins);
        return $this->createCustomResponse(['token' => $token]);
    }

    public function logout()
    {
        \auth('web')->logout();
        return true;
    }
}
