<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::all();
        foreach ($users as $user) {
            $randInt = random_int(2, 12);
            for ($i = 0; $i < $randInt; $i++) {
                $s = Service::query()->whereNotNull('parent_id')->inRandomOrder()->first();
                $p = random_int(100000, 100000000);
                $user->portfolios()->create([
                    'title' => $faker->text(20),
                    'description' => $faker->text(400),
                    'service_id' => $s->id,
                    'price' => $p,
                    'discount_price' => random_int($p / 10, $p - 10000),
                    'maintenance' => $faker->text(60),
                    'showing_phone_number' => $user->phone_number,
                    'images' => [
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                        $faker->imageUrl,
                    ],
                    'has_tel' => false,
                    'has_phone_number' => true,
                    'status' => 'published',
                ]);
            }
        }
    }
}
