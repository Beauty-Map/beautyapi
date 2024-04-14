<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Interfaces\MetaInterface;
use App\Interfaces\UserInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public UserInterface $userRepository;
    public MetaInterface $metaRepository;

    public function __construct(
        UserInterface $userRepository,
        MetaInterface $metaRepository,
    )
    {
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
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
