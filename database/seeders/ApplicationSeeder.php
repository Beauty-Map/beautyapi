<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Application::create([
            'app_id' => 'pol_map',
            'app_name' => 'پول مپ',
            'app_link' => 'https://polmap.ir',
        ]);
        Application::create([
            'app_id' => 'beauty_map',
            'app_name' => 'بیوتی مپ',
            'app_link' => 'https://beautymap.ir',
        ]);
    }
}
