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
use App\Http\Resources\BonusTransactionResource;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserLoginSimpleResource;
use App\Http\Resources\UserSelectedPlanResource;
use App\Interfaces\OtpInterface;
use App\Interfaces\PlanInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\UserPlanInterface;
use App\Models\Meta;
use App\Models\Otp;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

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
            } else {
                $request['password'] = Hash::make($request['password']);
                $this->userRepository->update($request->only([
                    'password',
                ]), $user->id);
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
                'referrer_code',
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
        $code = Helper::randomCode(6, 'number');
        Otp::query()->updateOrCreate([
            'phone_number' => $user->email,
            'code' => $code,
            'type' => Otp::REGISTER_OTP_TYPE
        ]);
        event(new SendRegisterOtpEvent($user));
        return $this->createCustomResponse($request['remember_token'], 201);
    }

    public function checkOtpCode(CheckRegisterOtpCodeRequest $request)
    {
        if(!$this->getOtpCodeByType($request)) {
            return $this->createError('INVALID_OTP_CODE_ERROR', Constants::INVALID_OTP_CODE_ERROR,404);
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        $this->userRepository->update([
            'is_active' => 1
        ], $user->id);
        $plan = $this->planRepository->find(1);
        $this->userPlanRepository->setPlan($user->id, $plan->id, null, null, -1, $plan->coins);
        $token =  $user->createToken(env('APP_NAME'))->accessToken;
        return new UserLoginSimpleResource($user, $token);
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
        return $this->createCustomResponse('done');
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
            $token =  $user->createToken(env('APP_NAME'))->accessToken;
            return new UserLoginResource($user, $token);
        } else{
            $meta = Meta::query()
                ->where('key', 'email')
                ->where('value', $request->get('email'))
                ->where('metaable_type', 'user')
                ->first();
            if ($meta) {
                $user = User::query()->findOrFail($meta->metaable_id);
                Auth::login($user);
                $token =  $user->createToken(env('APP_NAME'))->accessToken;
                return new UserLoginResource($user, $token);
            }
            return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
        }
    }

    public function adminLogin(LoginUserRequest $request)
    {
//        $request['phone_number'] = Helper::normalizePhoneNumber($request['phone_number']);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            /** @var User $user */
            $user = Auth::user();
            if (!$user->hasAnyRole(['admin', 'super-admin'])) {
                Auth::logout();
                return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
            }
            $token =  $user->createToken(env('APP_NAME'))->accessToken;
            return new UserLoginResource($user, $token);
        }
        else{
            return $this->createError('INVALID_LOGIN_ERROR', Constants::INVALID_LOGIN_ERROR, 422);
        }
    }

    public function own()
    {
        return new UserLoginResource($this->getAuth(), "");
    }

    public function statistics()
    {
        $auth = $this->getAuth();
        $month = \request()->input('month', null);
        $month = $month ? str_pad($month, 2, '0', STR_PAD_LEFT) : "01";
        $year = \request()->input('year', null);
        $app = \request()->input('app', 'pol_map');
        if ($app == 'pol_map') {
            $bonus = $auth->bonusTransactions()->where('app', 'polmap');
            $first = $auth->levelOneReferrals();
            $second = $auth->levelTwoReferrals();
            $third = $auth->levelThreeReferrals();
            $forth = $auth->levelFourReferrals();
            if ($year && $month) {
                $jalaliDate = Jalalian::fromFormat('Y/m/d', "$year/$month/01");
                $gregorianDate = $jalaliDate->toCarbon();
                $bonus = $bonus->where('created_at', '>=', $gregorianDate);
                $first = $first->where('created_at', '>=', $gregorianDate);
                $second = $second->where('u2.created_at', '>=', $gregorianDate);
                $third = $third->where('u3.created_at', '>=', $gregorianDate);
                $forth = $forth->where('u4.created_at', '>=', $gregorianDate);
            }
            $all = ($first->count() + $second->count() + $third->count() + $forth->count());
            $first = $first->count();
            $second = $second->count();
            $third = $third->count();
            $forth = $forth->count();
        } else {
            $bonus = $auth->bonusTransactions()->where('app', 'beauty');
            $all = $auth->levelOneReferrals()->count();
            $first = $auth->bonusTransactions()->where('app', 'beauty')->where('level', 0);
            $second = $auth->bonusTransactions()->where('app', 'beauty')->where('level', 1);
            $third = $auth->bonusTransactions()->where('app', 'beauty')->where('level', 2);
            $forth = $auth->bonusTransactions()->where('app', 'beauty')->where('level', 3);
            if ($year && $month) {
                $jalaliDate = Jalalian::fromFormat('Y/m/d', "$year/$month/01");
                $gregorianDate = $jalaliDate->toCarbon();
                $first = $first->where('created_at', '>=', $gregorianDate);
                $second = $second->where('created_at', '>=', $gregorianDate);
                $third = $third->where('created_at', '>=', $gregorianDate);
                $forth = $forth->where('created_at', '>=', $gregorianDate);
            }
            $first = $first->sum('amount');
            $second = $second->sum('amount');
            $third = $third->sum('amount');
            $forth = $forth->sum('amount');
        }
        return [
            'bonus' => $bonus->sum('amount'),
            'all' => $all,
            'first' => $first,
            'second' => $second,
            'third' => $third,
            'forth' => $forth,
        ];
    }

    public function statisticsDetails()
    {
        $auth = $this->getAuth();
        $month = \request()->input('month', null);
        $month = $month ? str_pad($month, 2, '0', STR_PAD_LEFT) : "01";
        $year = \request()->input('year', null);
        $app = \request()->input('app', 'pol_map');
        if ($app == 'pol_map') {
            $bonuses = $auth->bonusTransactions()->where('app', 'polmap');
        } else {
            $bonuses = $auth->bonusTransactions()->where('app', 'beauty');
        }
        if ($year && $month) {
            $jalaliDate = Jalalian::fromFormat('Y/m/d', "$year/$month/01");
            $gregorianDate = $jalaliDate->toCarbon();
            $bonuses = $bonuses->where('created_at', '>=', $gregorianDate);
        }
        if ($this->hasPage()) {
            $limit = $this->getLimit();
            $bonuses = $bonuses->paginate($limit);
        } else {
            $bonuses = $bonuses->get();
        }
        return BonusTransactionResource::collection($bonuses);
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
        if(!$this->getOtpCodeByType($request, Otp::FORGOT_PASSWORD_OTP_TYPE)) {
            return $this->createError('INVALID_OTP_CODE_ERROR', Constants::INVALID_OTP_CODE_ERROR,404);
        }
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $request['email']]);
        $token = Helper::randomCode(10);
        $this->userRepository->update([
            'remember_token' => $token,
            'is_active' => 1
        ], $user->id);
        return $this->createCustomResponse(['token' => $token]);
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
            ->where('created_at', '>=', Carbon::now()->subMinutes(2))
            ->first();
        if($validated) {
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        \auth('web')->logout();
        return true;
    }
}
