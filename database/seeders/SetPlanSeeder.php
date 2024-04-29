<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plan = Plan::query()->first();
        $users = User::all();
        /** @var User $user */
        foreach ($users as $user) {
            UserPlan::create([
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'status' => 'verified',
                'start_date' => null,
                'end_date' => null,
                'duration' => -1,
                'amount' => $plan->coins,
            ]);
            $goldWallet = $user->gold_wallet;
            $goldWallet->amount = $plan->coins;
            $goldWallet->save();
            $coinWallet = $user->coin_wallet;
        }
    }
}
