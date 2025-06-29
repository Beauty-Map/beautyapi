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
use Morilog\Jalali\Jalalian;

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
        $user = $this->getAuth();
        $app = $request->input('app', null);
        $paymentOptionID = $request->input('payment_id', null);
        $subscriptionID = $request->input('subscription_id', null);
        $coins = 0;
        $price = 0;
        $gift = 0;
        if ($paymentOptionID) {
            /** @var PaymentOption $paymentOption */
            $paymentOption = $this->paymentOptionRepository->findOneOrFail($request->get('payment_id', null));
            $price = $paymentOption->price;
            $coins = $paymentOption->coins;
            $gift = $paymentOption->gift;
        } else if ($subscriptionID) {
            /** @var Subscription $subscription */
            $subscription = Subscription::query()->findOrFail($subscriptionID);
            $price = $subscription->price * 1000000000;
        }

        $user = $this->getAuth();
//        $walletAddress = env('WALLET_ADDRESS');

        $now = Jalalian::now();
        $transactionId = $app.'_'.$user->id.'_'.$now->format('Ymd');

//        $paymentLink = "ton://transfer/$walletAddress?amount=$price&text=$transactionId&comment=$transactionId";
        Payment::query()->create([
            'user_id' => $user->id,
            'amount' => $price,
            'code' => $transactionId,
            'expire_at' => Carbon::now()->addMinutes(230),
            'coins' => $coins,
            'gift' => $gift,
            'payment_option_id' => $paymentOptionID,
            'subscription_id' => $subscriptionID,
        ]);
        return response()->json([
//            'payment_url' => $paymentLink,
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
                return $this->verifyBeautyMapPayment('beauty', $payment);
            }
            if (Str::startsWith($paymentCode, 'polmap')) {
                return $this->verifyPolMapPayment('polmap', $payment);
            }

        }
        return $this->createError('code_not_found', 'Code Not Found', 404);
    }

    public function verifyBeautyMapPayment(string $app, Payment $payment)
    {
        /** @var User $user */
        $user = $payment->user;
        $wc = $user->getCoinWallet();
        $wc->amount += ($payment->coins + $payment->gift);
        $wc->save();
        $payment->status = Payment::PAYED;
        $payment->save();
        return $user->distributeCoins($app, $payment->amount);
    }

    private function verifyPolMapPayment(string $app, Payment $payment)
    {
        /** @var User $user */
        $user = $payment->user;
        $subscription = Subscription::query()->findOrFail($payment->subscription_id);
        $date = $subscription->date;
        $start_at = $date['start_day'];
        $end_at = $date['end_day'];

        $user->subscriptions()->create([
            'start_at' => "$start_at 00:00:00",
            'end_at' => "$end_at 00:00:00",
            'subscription_id' => $subscription->id,
            'period' => $subscription->period
        ]);
        $payment->status = Payment::PAYED;
        $payment->save();
        return $user->distributeCoins($app, $payment->amount);
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
