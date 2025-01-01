<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $subscriptions = [
//            [
//                'title' => 'اشتراک یک ماهه',
//                'period' => 1,
//                'price' => 10000,
//            ],
//            [
//                'title' => 'اشتراک سه ماهه',
//                'period' => 3,
//                'price' => 27000,
//            ],
//            [
//                'title' => 'اشتراک شش ماهه',
//                'period' => 6,
//                'price' => 48000,
//            ],
//        ];

        $subscriptions = [
            [
                'title' => 'بهار',
                'period' => 3,
                'price' => 10000,
                'number' => 1
            ],
            [
                'title' => 'تابستان',
                'period' => 3,
                'price' => 27000,
                'number' => 2
            ],
            [
                'title' => 'پاییز',
                'period' => 3,
                'price' => 48000,
                'number' => 3
            ],
            [
                'title' => 'زمستان',
                'period' => 3,
                'price' => 58000,
                'number' => 4
            ],
        ];
        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
