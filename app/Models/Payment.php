<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const CREATED = 'created';
    const PAYED = 'payed';
    const FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'status',
        'amount',
        'code',
        'expire_at',
        'coins',
        'gift',
        'payment_option_id',
        'subscription_id',
    ];

    protected $with = [
        'user',
        'paymentOption',
        'subscription',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
