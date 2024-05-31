<?php

namespace Database\Seeders;

use App\Models\PaymentOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            ['coins' => 1000, 'gift' => 200, 'price' => 500000],
            ['coins' => 5000, 'gift' => 1000, 'price' => 2500000],
            ['coins' => 10000, 'gift' => 2000, 'price' => 5000000],
            ['coins' => 50000, 'gift' => 10000, 'price' => 25000000],
        ];

        foreach ($options as $option) {
            PaymentOption::query()->create($option);
        }
    }
}
