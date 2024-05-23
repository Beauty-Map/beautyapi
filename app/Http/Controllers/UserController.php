<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Helpers\Helper;
use App\Http\Requests\UpdateAltNumberRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Http\Resources\UserSimpleResource;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public UserInterface $userRepository;
    public MetaInterface $metaRepository;
    public OtpInterface $otpRepository;

    public function __construct(
        UserInterface $userRepository,
        MetaInterface $metaRepository,
        OtpInterface $otpRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
        $this->otpRepository = $otpRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function nearest()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $nearest = $this->userRepository->nearestByPagination($page, $limit, 'desc');
        } else {
            $nearest = $this->userRepository->nearest('desc');
        }
            return UserSimpleResource::collection($nearest);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserProfileUpdateRequest $request, int $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(UserProfileUpdateRequest $request)
    {
        $auth = $this->getAuth();
        $request = $request->all();
        DB::beginTransaction();
        if ($request['full_name']) {
            $auth->update(['full_name' => $request['full_name']]);
            unset($request['full_name']);
        }
        if ($request['city_id']) {
            $auth->update(['city_id' => $request['city_id']]);
            unset($request['city_id']);
        }
        if ($request['birth_date']) {
            $auth->update(['birth_date' => $request['birth_date']]);
            unset($request['birth_date']);
        }
        $res = $this->metaRepository->insertOrAdd($request, $auth->id, 'user');
        if ($res) {
            DB::commit();
            return $this->createCustomResponse(1);
        }
        DB::rollBack();
        return $this->createError('error', Constants::UNDEFINED_ERROR, 422);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $auth = $this->getAuth();
        if (Hash::check($request->get('old_password'), $auth->password)) {
            if ($auth->update(['password' => Hash::make($request->get('password'))])) {
                \auth('web')->logout();
                return true;
            }
            return $this->createError('error', Constants::UNDEFINED_ERROR, 500);
        } else {
            return $this->createError('old_password', Constants::INVALID_PASSWORD_ERROR, 422);
        }
    }

    public function sendOtpForAltNumber(UpdateAltNumberRequest $request)
    {
        $otp = Helper::randomCode(4, 'digit');
        $this->otpRepository->make([
            'phone_number' => $request->get('alt_number'),
            'code' => $otp,
            'type' => 'alt_number',
        ]);
        return $otp;
    }

    public function updateAltNumber(UpdateAltNumberRequest $request)
    {
        $auth = $this->getAuth();
        $code = $request->input('code', '');
        $altNumber = $request->input('alt_number', '');
        if (!$code) {
            return $this->createError('code', Constants::INVALID_OTP_CODE_ERROR, 422);
        }
        $data = [
            'phone_number' => $altNumber,
            'code' => $code,
            'type' => 'alt_number'
        ];
        $otp = $this->otpRepository->validate($data);
        if (!$otp) {
            return $this->createError('code', Constants::INVALID_OTP_CODE_ERROR, 422);
        }
        return $this->createCustomResponse('done', 200);
    }

    public function deleteAccount()
    {
        $user = $this->getAuth();
        if (!$user->can('delete-own-account')) {
            return $this->createError('delete-account', Constants::ACCESS_ERROR, 403);
        }
        return $this->userRepository->delete($this->getAuth()->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
