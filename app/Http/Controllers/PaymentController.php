<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Http\Requests\VerifyPaymentBeautymapRequest;
use App\Interfaces\PaymentOptionInterface;
use App\Interfaces\PlanInterface;
use App\Models\Payment;
use App\Models\PaymentOption;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Types\Relations\Car;

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
        /** @var PaymentOption $paymentOption */
        $paymentOption = $this->paymentOptionRepository->findOneOrFail($request->get('payment_id', null));
        $user = $this->getAuth();
        $walletAddress = env('WALLET_ADDRESS');

        $transactionId = "pay_".Carbon::now()->unix();

        $price = $paymentOption->price;
        $paymentLink = "ton://transfer/$walletAddress?amount=$price&text=$transactionId&comment=$transactionId";
        Payment::query()->create([
            'user_id' => $user->id,
            'amount' => $price,
            'code' => $transactionId,
            'expire_at' => Carbon::now()->addMinutes(10),
            'coins' => $paymentOption->coins,
            'payment_option_id' => $paymentOption->id,
        ]);
        return response()->json(['payment_url' => $paymentLink]);
    }

    public function verify(VerifyPaymentBeautymapRequest $request)
    {
        $paymentCode = $request->get('code', '');
        if ($paymentCode) {
            /** @var Payment $payment */
            $payment = Payment::query()
                ->where('code', $paymentCode)
                ->where('status', Payment::CREATED)
                ->first();
            if (!$payment) {
                return $this->createError('not_found', 'Not Found', 404);
            }
            if (Carbon::now()->isAfter($payment->expire_at)) {
                $payment->status = Payment::FAILED;
                $payment->save();
                return $this->createError('expired', 'Expired', 401);
            }
            /** @var User $user */
            $user = $payment->user;
            $wc = $user->getCoinWallet();
            $wc->amount += $payment->coins;
            $wc->save();
            $payment->status = Payment::PAYED;
            $payment->save();
            return $user->distributeCoins($payment->amount);
        }
        return $this->createError('code_not_found', 'Code Not Found', 404);
    }

    public function status(Request $request)
    {
        $paymentCode = $request->get('code', '');
        if ($paymentCode) {
            /** @var Payment $payment */
            $payment = Payment::query()
                ->where('code', $paymentCode)
                ->where('status', Payment::CREATED)
                ->firstOrFail();
            return $this->createCustomResponse($payment->status);
        } else {
            return $this->createError('payment', 'not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return $payment;
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
