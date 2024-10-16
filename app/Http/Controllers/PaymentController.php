<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Interfaces\PaymentOptionInterface;
use App\Interfaces\PlanInterface;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PlanInterface $planRepository;
    protected PaymentOptionInterface $paymentOptionRepository;

    public function __construct(
        PlanInterface $planRepository,
        PaymentOptionInterface $paymentOptionRepository,
    )
    {
        $this->planRepository = $planRepository;
        $this->paymentOptionRepository = $paymentOptionRepository;
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
    public function store(PaymentCreateRequest $request)
    {
        $paymentOption = $this->paymentOptionRepository->findOneOrFail($request->get('payment_id', null));
        $user = $this->getAuth();
        $walletAddress = env('WALLET_ADDRESS');

        $transactionId = base64_encode("user:$user->id,payment:$paymentOption->id");

        $price = $paymentOption->price;
//        $price *=  10 ** 9;
        $price = 100;
        $walletAddress = "UQCdN7eLj50eFEvwNBAkrvEN3Z8_ETg60uAfxfQI4a3L7BCR";
        $paymentLink = "ton://transfer/$walletAddress?amount=$price&text=$transactionId&comment=$transactionId";

        return response()->json(['payment_url' => $paymentLink]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
