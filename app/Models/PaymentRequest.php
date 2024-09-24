<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    const WITHDRAW_TYPE = 'withdraw';
    const DEPOSIT_TYPE = 'deposit';

    const CREATED_STATUS = 'created';
    const ACCEPTED_STATUS = 'accepted';
    const REJECTED_STATUS = 'rejected';

    protected $fillable = [
        'type',
        'amount',
        'user_id',
        'status'
    ];

    protected $appends = [
        'type_fa',
        'status_fa',
    ];

    public function getTypeFaAttribute() {
        return match ($this->type) {
            self::WITHDRAW_TYPE => 'برداشت موجودی',
            default => 'واریز وجه',
        };
    }

    public function getStatusFaAttribute() {
        return match ($this->status) {
            self::CREATED_STATUS => 'درانتظار بررسی',
            self::ACCEPTED_STATUS => 'تایید شده',
            default => 'رد شده',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
