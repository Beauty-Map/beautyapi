<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;

    const STATUS_PAYED = 'payed';
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PAY = 'in_pay';

    protected $fillable = [
        'app',
        'status',
        'amount',
        'user_id',
        'referrer_id',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }
}
