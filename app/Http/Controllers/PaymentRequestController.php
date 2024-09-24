<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\PaymentRequestCreateRequest;
use App\Http\Resources\PaymentRequestResource;
use App\Interfaces\PaymentRequestInterface;
use App\Models\User;

class PaymentRequestController extends Controller
{
    protected PaymentRequestInterface $paymentRequestRepository;

    public function __construct(
        PaymentRequestInterface $paymentRequestRepository,
    )
    {
        $this->paymentRequestRepository = $paymentRequestRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function indexOwn()
    {
        $auth = $this->getAuth();
        $data = [
            'user_id' => $auth->id
        ];
        if ($this->hasPage()) {
            $page = $this->getPage();
            $limit = $this->getLimit();
            $requests = $this->paymentRequestRepository->findByPaginate($data, $page, $limit, 'created_at', 'desc');
        } else {
            $requests = $this->paymentRequestRepository->findBy($data, 'created_at', 'desc');
        }
        return PaymentRequestResource::collection($requests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequestCreateRequest $request)
    {
        $auth = $this->getAuth();
        $coins = $auth->coins;
        if ($request['amount'] > $coins) {
            return $this->createError('amount', Constants::NOT_ENOUGH_COINS, 422);
        }
        $request['user_id'] = $auth->id;
        $paymentRequest = $this->paymentRequestRepository->create($request->only([
            'amount',
            'user_id',
        ]));
        return new PaymentRequestResource($paymentRequest);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return new PaymentRequestResource($this->paymentRequestRepository->findOneOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->paymentRequestRepository->delete($id);
    }
}
