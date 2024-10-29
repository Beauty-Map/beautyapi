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
use App\Http\Resources\UserSimpleResource;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\UserInterface;
use App\Models\Portfolio;
use App\Models\User;
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
     * Display the specified resource.
     */
    public function addView(int $id)
    {
        /** @var User $user */
        $user = $this->userRepository->find($id);
        $user?->addView();
        return true;
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
        if (array_key_exists('full_name', $request) && $request['full_name']) {
            $auth->update(['full_name' => $request['full_name']]);
            unset($request['full_name']);
        }
        if (array_key_exists('city_id', $request) && $request['city_id']) {
            $auth->update(['city_id' => $request['city_id']]);
            unset($request['city_id']);
        }
        if (array_key_exists('birth_date', $request) && $request['birth_date']) {
            $auth->update(['birth_date' => $request['birth_date']]);
            unset($request['birth_date']);
        }
        if (array_key_exists('phone_number', $request) && $request['phone_number']) {
            $auth->update(['phone_number' => $request['phone_number']]);
            unset($request['phone_number']);
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
        try {
            if (array_key_exists('full_name', $request) && $request['full_name']) {
                $auth->update(['full_name' => $request['full_name']]);
                unset($request['full_name']);
            }
            if (array_key_exists('phone_number', $request) && $request['phone_number']) {
                $auth->update(['phone_number' => $request['phone_number']]);
                unset($request['phone_number']);
            }
            if (array_key_exists('city_id', $request) && $request['city_id']) {
                $auth->update(['city_id' => $request['city_id']]);
                unset($request['city_id']);
            }
            if (array_key_exists('birth_date', $request) && $request['birth_date']) {
                $auth->update(['birth_date' => $request['birth_date']]);
                unset($request['birth_date']);
            }
            if (array_key_exists('location', $request) && $request['location']) {
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
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->createError('error', 'در حال حاضر سرور پاسخ نمی دهد.', 500);
        }
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
        $auth = $this->getAuth();
        $altNumber = $request->input('alt_number', '');
        if (!$altNumber) {
            return $this->createError('alt_number', Constants::INVALID_EMAIL_ERROR, 422);
        }
        return $auth->setMeta('alt_number', $altNumber);
//        $otp = Helper::randomCode(6, 'digit');
//        $this->otpRepository->make([
//            'email' => $request->get('alt_number'),
//            'code' => $otp,
//            'type' => 'alt_number',
//        ]);
//        return $otp;
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
            'email' => $altNumber,
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

    public function indexFavouriteArtists()
    {
        $auth = $this->getAuth();
        $artists = $auth->likedItemsByTrait(User::class);
        $data['data'] = ArtistResource::collection($artists);
        return $data;
    }

    public function like(int $id)
    {
        $auth = $this->getAuth();
        /** @var User $user */
        $user = $this->userRepository->findOneOrFail($id);
        return $user->toggleLike($auth->id);
    }

    public function indexBestReferrals()
    {
        $auth = $this->getAuth();
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $users = User::query()->where('referrer_code', $auth->referral_code)->paginate($limit);
        } else {
            $users = User::query()->where('referrer_code', $auth->referral_code)->get();
        }
        return UserSimpleResource::collection($users);
    }

    public function refers()
    {
        $startTime = \request()->input('startTime', null);
        $endTime = \request()->input('endTime', null);
        $auth = $this->getAuth();
        $query = User::query()->where('referrer_code', $auth->referral_code);
        if ($startTime) {
            $query = $query->where('created_at', '>=', $startTime);
        }
        if ($endTime) {
            $query = $query->where('created_at', '<=', $endTime);
        }
        return $query->count();
    }
}
