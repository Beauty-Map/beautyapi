<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        Service::factory(10)->create();
        $services = Service::all();
        foreach ($services as $service) {
            $randInt = random_int(5, 15);
            for ($i = 0; $i < $randInt; $i++) {
                Service::create([
                    'image' => $faker->imageUrl,
                    'title' => $faker->text(20),
                    'is_active' => true,
                    'parent_id' => $service->id
                ]);
            }
        }
    }
}
