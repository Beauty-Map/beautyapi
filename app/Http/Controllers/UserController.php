<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Helpers\Helper;
use App\Http\Requests\ArtistProfileUpdateRequest;
use App\Http\Requests\DoArtistAgreement;
use App\Http\Requests\UpdateAltNumberRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Http\Requests\LadderRequest;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\UserNearResource;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public UserInterface $userRepository;
    public MetaInterface $metaRepository;
    public OtpInterface $otpRepository;
    public PortfolioInterface $portfolioRepository;

    public function __construct(
        UserInterface $userRepository,
        MetaInterface $metaRepository,
        OtpInterface $otpRepository,
        PortfolioInterface $portfolioRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
        $this->otpRepository = $otpRepository;
        $this->portfolioRepository = $portfolioRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function nearest()
    {
        $lat = \request()->input('lat', null);
        $lng = \request()->input('lng', null);
        $filter = [
            'lat' => $lat,
            'lng' => $lng,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $nearest = $this->userRepository->nearestByPagination($filter, $page, $limit, 'desc');
        } else {
            $nearest = $this->userRepository->nearest($filter, 'desc');
        }
            return UserNearResource::collection($nearest);
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
        return new ArtistResource($this->userRepository->findOneOrFail($id));
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

    public function updateArtistProfile(ArtistProfileUpdateRequest $request)
    {
        $auth = $this->getAuth();
        $request = $request->all();
        DB::beginTransaction();
        if ($request['full_name']) {
            $auth->update(['full_name' => $request['full_name']]);
            unset($request['full_name']);
        }
        if ($request['phone_number']) {
            $auth->update(['phone_number' => $request['phone_number']]);
            unset($request['phone_number']);
        }
        if ($request['city_id']) {
            $auth->update(['city_id' => $request['city_id']]);
            unset($request['city_id']);
        }
        if ($request['birth_date']) {
            $auth->update(['birth_date' => $request['birth_date']]);
            unset($request['birth_date']);
        }
        if ($request['location']) {
            $auth->update(['lat' => $request['location']['lat'], 'lng' => $request['location']['lng']]);
            unset($request['location']);
        }
        $res = $this->metaRepository->insertOrAdd($request, $auth->id, 'user');
        if ($res) {
            $auth->assignRole('artist');
            DB::commit();
            return $this->createCustomResponse(1);
        }
        DB::rollBack();
        return $this->createError('error', Constants::UNDEFINED_ERROR, 422);
    }

    public function doArtistAgreement(DoArtistAgreement $request)
    {
        $auth = $this->getAuth();
        $request = $request->all();
        DB::beginTransaction();
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

    public function doLadder(LadderRequest $request)
    {
        if ($request['type'] == 'all_portfolios') {
            return $this->userRepository->doLadder();
        }
        return match ($request['type']) {
            'all_portfolios' => $this->portfolioRepository->doLadder($request->toArray()),
            'some_portfolios' => $this->portfolioRepository->doLadder($request->toArray()),
            'profile' => $this->userRepository->doLadder(),
            default => $this->createError('type', Constants::LADDERING_TYPE_ERROR, 422),
        };
    }
}
