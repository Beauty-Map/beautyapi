<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\PaymentRequestCreateRequest;
use App\Http\Resources\PaymentRequestResource;
use App\Interfaces\PaymentRequestInterface;
use App\Models\BonusTransaction;
use App\Models\PaymentRequest;
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
        $bonuses = $auth->bonusTransactions()->where('status', BonusTransaction::STATUS_PENDING);
        $amount = $bonuses->sum('amount');
        if ($amount < 10) {
            return $this->createError('error', 'حداقل مبلغ قابل برداشت 10 TON می باشد', 422);
        }
        $openRequests = PaymentRequest::query()
            ->where('type', PaymentRequest::WITHDRAW_TYPE)
            ->where('status', 'created')
            ->count();
        if ($openRequests > 0) {
            return $this->createError('error', 'شما یک برداشت در حال بررسی دارید.', 422);
        }
        $paymentRequest = $this->paymentRequestRepository->create([
            'amount' => $amount,
            'user_id' => $auth->id,
        ]);
        $bonuses->update([
            'status' => BonusTransaction::STATUS_IN_PAY,
        ]);
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
