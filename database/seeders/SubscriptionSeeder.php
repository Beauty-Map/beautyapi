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
        $subscriptions = [
            [
                'title' => 'اشتراک یک ماهه',
                'period' => 1,
                'price' => 10000,
            ],
            [
                'title' => 'اشتراک سه ماهه',
                'period' => 3,
                'price' => 27000,
            ],
            [
                'title' => 'اشتراک شش ماهه',
                'period' => 6,
                'price' => 48000,
            ],
        ];
        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
