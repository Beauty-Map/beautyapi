<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Http\Requests\VerifyPaymentBeautymapRequest;
use App\Interfaces\PaymentOptionInterface;
use App\Interfaces\PlanInterface;
use App\Models\Payment;
use App\Models\PaymentOption;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $app = $request->input('app', null);
        $paymentOptionID = $request->input('payment_id', null);
        $subscriptionID = $request->input('subscription_id', null);
        $coins = 0;
        $price = 0;
        if ($paymentOptionID) {
            /** @var PaymentOption $paymentOption */
            $paymentOption = $this->paymentOptionRepository->findOneOrFail($request->get('payment_id', null));
            $price = $paymentOption->price;
            $coins = $paymentOption->coins;
        } else if ($subscriptionID) {
            /** @var Subscription $subscription */
            $subscription = Subscription::query()->findOrFail($subscriptionID);
            $price = $subscription->price * 1000000000;
        }

        $user = $this->getAuth();
        $walletAddress = env('WALLET_ADDRESS');

        $transactionId = $app.'_'.Carbon::now()->unix();
        $transactionId = urlencode($transactionId);

        $paymentLink = "ton://transfer/$walletAddress?amount=$price&text=$transactionId&comment=$transactionId";
        Payment::query()->create([
            'user_id' => $user->id,
            'amount' => $price,
            'code' => $transactionId,
            'expire_at' => Carbon::now()->addMinutes(230),
            'coins' => $coins,
            'payment_option_id' => $paymentOptionID,
            'subscription_id' => $subscriptionID,
        ]);
        return response()->json([
            'payment_url' => $paymentLink,
            'payment_id' => $transactionId,
        ]);
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
            if (Str::startsWith($paymentCode, 'beauty')) {
                return $this->verifyBeautyMapPayment($payment);
            }
            if (Str::startsWith($paymentCode, 'polmap')) {
                return $this->verifyPolMapPayment($payment);
            }

        }
        return $this->createError('code_not_found', 'Code Not Found', 404);
    }

    public function verifyBeautyMapPayment(Payment $payment)
    {
        /** @var User $user */
        $user = $payment->user;
        $wc = $user->getCoinWallet();
        $wc->amount += ($payment->coins + $payment->gift);
        $wc->save();
        $payment->status = Payment::PAYED;
        $payment->save();
        return $user->distributeCoins($payment->amount);
    }

    private function verifyPolMapPayment(Payment $payment)
    {
        /** @var User $user */
        $user = $payment->user;
        $subscription = Subscription::query()->findOrFail($payment->subscription_id);
        $user->subscriptions()->create([
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonths($subscription->period),
            'subscription_id' => $subscription->id,
            'period' => $subscription->period
        ]);
        $payment->status = Payment::PAYED;
        return $payment->save();
    }

    public function status(Request $request)
    {
        $paymentCode = $request->get('code', '');
        if ($paymentCode) {
            /** @var Payment $payment */
            $payment = Payment::query()
                ->where('code', $paymentCode)
                ->firstOrFail();
            return $this->createCustomResponse($payment->status);
        } else {
            return $this->createError('payment', 'not found', 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        return Payment::query()->where('code', $code)->firstOrFail();
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
