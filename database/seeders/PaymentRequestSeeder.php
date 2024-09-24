<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $l = random_int(10, 24);
            for ($i = 0; $i < $l; $i++) {
                $user->paymentRequests()->create([
                    'type' => 'withdraw',
                    'amount' => random_int(10000, 100000),
                    'status' => 'accepted'
                ]);
            }
        }
    }
}
