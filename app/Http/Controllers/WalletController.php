<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletDecreaseRequest;
use App\Http\Requests\WalletIncreaseRequest;
use App\Interfaces\WalletInterface;

class WalletController extends Controller
{
    public WalletInterface $walletRepository;

    public function __construct(
        WalletInterface $walletRepository,
    )
    {
        $this->walletRepository = $walletRepository;
    }

    public function increase(WalletIncreaseRequest $request)
    {
        $auth = $this->getAuth();
        $req = $this->createIncreaseRequest($auth, $request->amount);
        $res = $this->getPaymentUrl($req, $request->amount);
        return $this->createCustomResponse($res);
    }

    private function createIncreaseRequest(\App\Models\User $auth, int $amount)
    {
        $res = [];
        return $res;
    }

    private function getPaymentUrl(array $req, int $amount)
    {
        $res = [];
        return $res;
    }
}
