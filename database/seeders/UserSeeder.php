<?php

namespace Database\Seeders;

use App\Models\BonusTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        /** @var User $superAdmin */
        $superAdmin = User::create([
            'full_name' => 'سوپرادمین',
            'email' => 'saeedhpro@gmail.com',
            'phone_number' => '09381412419',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => 1,
            'city_id' => 1225,
        ]);
        $superAdminRole = Role::query()->where('name', 'super-admin')->first();
        $adminRole = Role::query()->where('name', 'admin')->first();
        $superAdmin->assignRole($superAdminRole);
        $superAdmin->assignRole($adminRole);

        /** @var User $admin */
        $admin = User::create([
            'full_name' => 'ادمین',
            'email' => 'saeedhpro2@gmail.com',
            'phone_number' => '09381412418',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => 1,
            'city_id' => 1225,
        ]);
        $admin->assignRole($adminRole);

        $userRole = Role::query()->where('name', 'user')->first();
        $marketerRole = Role::query()->where('name', 'marketer')->first();
        $artistRole = Role::query()->where('name', 'artist')->first();
        $users = [];
        for ($i = 0; $i < 10; $i++) {
           try {
               /** @var User $u */
               $u = User::create([
                   'full_name' => $faker->name,
                   'email' => $faker->unique()->safeEmail,
                   'phone_number' => $faker->unique()->e164PhoneNumber(),
                   'password' => Hash::make('password'),
                   'remember_token' => Str::random(10),
                   'is_active' => 1,
                   'city_id' => 1225,
               ]);
               $u->assignRole($userRole, $marketerRole, $artistRole);
               $users[] = $u;
           } catch (\Exception $e) {

            }
        }
        for ($j = 0; $j < 20; $j++) {
            $randInt = random_int(3, 10);
            $users = $this->createUsers($users, count($users) * $randInt);
        }
    }

    public function createUsers($parentUsers = [], $count = 2)
    {
        $userRole = Role::query()->where('name', 'user')->first();
        $marketerRole = Role::query()->where('name', 'marketer')->first();
        $artistRole = Role::query()->where('name', 'artist')->first();
        $users = [];
        $faker = Faker::create();
        for ($i = 0; $i < 20; $i++) {
            $randomKey = array_rand($parentUsers);
            $randomUser = $parentUsers[$randomKey];
            /** @var User $u */
            $u = User::create([
                'full_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->unique()->e164PhoneNumber(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'is_active' => 1,
                'city_id' => 1225,
                'referrer_code' => $randomUser->referral_code,
            ]);
            $u->assignRole($userRole, $marketerRole, $artistRole);
            $users[] = $u;
            $this->createActivities($u);
        }
        return $users;
    }

    private function createActivities(User $u)
    {
        $randInt = random_int(10, 40);
        for ($i = 0; $i < $randInt; $i++) {
            $amount = random_int(10, 10000);
            $u->activities()->create([
                'action' => 'purchase',
                'body' => [
                    'status' => 'payed',
                    'amount' => $amount,
                ],
            ]);
            $bonuses = $u->distributeCoins($amount);
            foreach ($bonuses as $bonus) {
                $p = random_int(10, 90);
                $status = $this->weightedRandom(
                    [
                        BonusTransaction::STATUS_PENDING,
                        BonusTransaction::STATUS_PAYED,
                    ],
                    [$p, 100 - $p]
                );
                if ($status == BonusTransaction::STATUS_PAYED) {
                    $bonus->update([
                        'status' => BonusTransaction::STATUS_PAYED,
                    ]);
                }
            }
        }
    }

    function weightedRandom($items, $weights) {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        foreach ($items as $index => $item) {
            if ($random <= $weights[$index]) {
                return $item;
            }
            $random -= $weights[$index];
        }
    }
}
