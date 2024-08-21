<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
}
