<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceAndCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Province::all()->count() == 0) {
            ini_set('memory_limit', '-1');
            DB::unprepared( file_get_contents( "database/seeders/dump.sql" ) );
        }
    }
}
