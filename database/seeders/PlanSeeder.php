<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'title' => 'عادی',
                'coins' => 1000,
                'portfolio_count' => 10,
                'laddering_count' => 10,
                'star_count' => 5,
                'has_blue_tick' => true,
                'image_upload_count' => 30,
                'has_discount' => false,
            ],
            [
                'title' => 'برنزی',
                'coins' => 2000,
                'portfolio_count' => 20,
                'laddering_count' => 20,
                'star_count' => 10,
                'has_blue_tick' => true,
                'image_upload_count' => 60,
                'has_discount' => false,
            ],
            [
                'title' => 'نقره ای',
                'coins' => 3000,
                'portfolio_count' => 30,
                'laddering_count' => 30,
                'star_count' => 15,
                'has_blue_tick' => true,
                'image_upload_count' => 90,
                'has_discount' => true,
            ],
            [
                'title' => 'طلایی',
                'coins' => 4000,
                'portfolio_count' => 40,
                'laddering_count' => 40,
                'star_count' => 20,
                'has_blue_tick' => true,
                'image_upload_count' => 120,
                'has_discount' => true,
            ],
            [
                'title' => 'الماسی',
                'coins' => 5000,
                'portfolio_count' => 50,
                'laddering_count' => 50,
                'star_count' => 25,
                'has_blue_tick' => true,
                'image_upload_count' => 150,
                'has_discount' => true,
            ],
        ];
        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
