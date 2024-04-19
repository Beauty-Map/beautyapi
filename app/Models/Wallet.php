<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'walletable_id',
        'walletable_type',
    ];

    public function walletable()
    {
        return $this->morphTo();
    }
}
