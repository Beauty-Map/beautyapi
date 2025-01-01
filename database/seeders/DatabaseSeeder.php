<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
//        $this->call(ProvinceAndCitySeeder::class);
//        $this->call(PlanSeeder::class);
//        $this->call(ServiceSeeder::class);
//        $this->call(RoleAndPermissionSeeder::class);
//        $this->call(UserSeeder::class);
//        $this->call(SetPlanSeeder::class);
//        $this->call(TicketSubjectSeeder::class);
//        $this->call(PaymentOptionSeeder::class);
//        $this->call(ApplicationSeeder::class);
//        $this->call(PortfolioSeeder::class);
//        $this->call(PaymentRequestSeeder::class);
//        $this->call(CourseSeeder::class);
        $this->call(SubscriptionSeeder::class);
//        $this->call(MainSliderSeeder::class);
    }
}
