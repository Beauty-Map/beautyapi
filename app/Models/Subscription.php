<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'period',
        'price',
        'number',
    ];

    protected $appends = [
        'date',
//        'next_year_spring'
    ];

    public function getDateAttribute()
    {
        $now = Jalalian::now()->getFirstDayOfYear();
        if ($this->number > 1) {
            $now = $now->addMonths(($this->number - 1) * 3);
        }
        $year = $now->getYear();
        return [
            'year' => $year,
            'start_day1' => $now->getFirstDayOfQuarter()->format('Y-m-d'),
            'start_day' => $now->getFirstDayOfQuarter()->toCarbon()->format('Y-m-d'),
            'end_day' => $now->getEndDayOfQuarter()->toCarbon()->format('Y-m-d'),
            'jallali_start_day' => $now->getFirstDayOfQuarter()->format('Y-m-d'),
            'jallali_end_day' => $now->getEndDayOfQuarter()->format('Y-m-d'),
            'is_payable' => Jalalian::now()->getQuarter() == $this->number,
        ];
    }
}
