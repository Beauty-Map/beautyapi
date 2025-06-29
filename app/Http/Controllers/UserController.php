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
use App\Http\Requests\UserSetWalletAddressRequest;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\UserNearResource;
use App\Http\Resources\UserSimpleResource;
use App\Interfaces\MetaInterface;
use App\Interfaces\OtpInterface;
use App\Interfaces\PortfolioInterface;
use App\Interfaces\UserInterface;
use App\Models\Ladder;
use App\Models\Meta;
use App\Models\Portfolio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

    public function getOwnPlan()
    {
        $auth = $this->getAuth();
        return $auth->getSelectedPlan();
    }

    /**
     * Display a listing of the resource.
     */
    public function nearest()
    {
        $provinceID = \request()->input('province_id', null);
        $cityID = \request()->input('city_id', null);
        $filter = [
            'province_id' => $provinceID,
            'city_id' => $cityID,
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $nearest = $this->userRepository->nearestByPagination($filter, $page, $limit, 'desc');
            if (count($nearest) == 0) {
                unset($filter['city_id']);
                $nearest = $this->userRepository->nearestByPagination($filter, $page, $limit, 'desc');
            }
        } else {
            $nearest = $this->userRepository->nearest($filter, 'desc');
            if (count($nearest) == 0) {
                unset($filter['city_id']);
                $nearest = $this->userRepository->nearest($filter, 'desc');
            }
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

    public function setWalletAddress(UserSetWalletAddressRequest $request)
    {
        $auth = $this->getAuth();
        $data = [
            'ton_wallet_address' => $request->input('ton_wallet_address', '')
        ];
        DB::beginTransaction();
        $res = $this->metaRepository->insertOrAdd($data, $auth->id, 'user');
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
            if (array_key_exists('referrer_code', $request) && $request['referrer_code'] && !$auth->referrer_code) {
                $auth->update(['referrer_code' => $request['referrer_code']]);
                unset($request['referrer_code']);
            }
            if (array_key_exists('features', $request) && $request['features']) {
                $auth->features()->sync($request['features']);
                unset($request['features']);
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
            return $this->createError('old_password', Constants::INVALID_OLD_PASSWORD_ERROR, 422);
        }
    }

    public function sendOtpForAltNumber(UpdateAltNumberRequest $request)
    {
        $auth = $this->getAuth();
        $altNumber = $request->input('email', '');
        if (!$altNumber) {
            return $this->createError('email', Constants::INVALID_EMAIL_ERROR, 422);
        }
        $metaCount = Meta::query()
            ->where('key', 'email')
            ->where('value', $altNumber)
            ->whereNot('metaable_id', $auth->id)
            ->count();

        $emailCount = User::query()
            ->where('email', $altNumber)
            ->count();

        if ($metaCount > 0 || $emailCount > 0) {
            return $this->createError('email', Constants::INVALID_EMAIL, 422);
        }
        $auth->setMeta('email', $altNumber);
        return $this->createCustomResponse('done', 200);
    }

    public function deleteAccount()
    {
        $user = $this->getAuth();
        DB::beginTransaction();
        $portfolios = $user->portfolios();
        /** @var Portfolio $portfolio */
        foreach ($portfolios as $portfolio) {
            $portfolio->metas()->delete();
            $portfolio->delete();
        }
        $user->metas()->delete();
        $user->services()->delete();
        $user->plans()->delete();
        $user->wallets()->delete();
        $user->bonusTransactions()->delete();
        $user->likes()->delete();
        $user->paymentRequests()->delete();
        $user->activities()->delete();
        $user->subscriptions()->delete();
        $user->delete();
        DB::commit();
        return true;
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
        return match ($request['type']) {
            'all_portfolios', 'some_portfolios' => $this->doLadderPortfolios($request),
            'profile' => $this->userRepository->doLadder(),
            default => $this->createError('type', Constants::LADDERING_TYPE_ERROR, 422),
        };
    }

    public function doLadderPortfolios(LadderRequest $request): bool | JsonResponse
    {
        $auth = $this->getAuth();
        DB::beginTransaction();
        try {
            $plan = $auth->getSelectedPlan()->plan;
            $userLadders = $auth->portfolios()->where('laddered_at', '>=', Carbon::now()->subDays(2))->count();
            $wallet = $auth->getCoinWallet();
            if ($request['type'] == 'all_portfolios') {
                $portfolios = $auth->portfolios;
                if ($plan->portfolio_count - $userLadders < count($portfolios)) {
                    return $this->createError('do_ladder', Constants::LADDERING_COUNT_ERROR, 422);
                }
                $requiredCoins = count($portfolios) * 6;
                if ($wallet->amount < $requiredCoins) {
                    return $this->createError('do_ladder', Constants::LADDERING_PRICE_ERROR, 422);
                }
                /** @var Portfolio $portfolio */
                foreach ($portfolios as $portfolio) {
                    $portfolio->update(['laddered_at' => Carbon::now()]);
                    $portfolio->save();
                }
                $wallet->update([
                    'amount' => $wallet->amount - $requiredCoins
                ]);
                DB::commit();
                return true;
            } else {
                $portfolios = $request['data'];
                if ($plan->portfolio_count - $userLadders < count($portfolios) ) {
                    return $this->createError('do_ladder', Constants::LADDERING_COUNT_ERROR, 422);
                }
                $requiredCoins = count($portfolios) * 10;
                if ($wallet->amount < $requiredCoins) {
                    return $this->createError('do_ladder', Constants::LADDERING_PRICE_ERROR, 422);
                }
                foreach ($portfolios as $portfolio) {
                    $p = Portfolio::query()->find($portfolio);
                    if ($p) {
                        $p->update(['laddered_at' => Carbon::now()]);
                        $p->save();
                    }
                }
            }
            $wallet->update([
                'amount' => $wallet->amount - $requiredCoins
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
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
