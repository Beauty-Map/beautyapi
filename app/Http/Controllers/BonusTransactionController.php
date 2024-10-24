<?php

namespace App\Http\Controllers;

use App\Http\Resources\BonusTransactionResource;
use App\Models\BonusTransaction;
use Illuminate\Http\Request;

class BonusTransactionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $bonuses = BonusTransaction::query()->paginate($limit);
        } else {
            $bonuses = BonusTransaction::query()->get();
        }
        return BonusTransactionResource::collection($bonuses);
    }

    /**
     * Display a listing of the resource.
     */
    public function ownIndex()
    {
        $auth = $this->getAuth();
        $filter = ['user_id' => $auth->id];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $bonuses = BonusTransaction::query()->where($filter)->paginate($limit);
        } else {
            $bonuses = BonusTransaction::query()->where($filter)->get();
        }
        return BonusTransactionResource::collection($bonuses);
    }

    /**
     * Display a listing of the resource.
     */
    public function userIndex(int $id)
    {
        $filter = ['user_id' => $id];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $bonuses = BonusTransaction::query()->where($filter)->paginate($limit);
        } else {
            $bonuses = BonusTransaction::query()->where($filter)->get();
        }
        return BonusTransactionResource::collection($bonuses);
    }

    public function ownIncome()
    {
        $auth = $this->getAuth();
        $appID = \request()->input('app_id', null);
        return $auth->bonusTransactions()
            ->where('status', BonusTransaction::STATUS_PAYED)
            ->sum('amount');
    }
}
