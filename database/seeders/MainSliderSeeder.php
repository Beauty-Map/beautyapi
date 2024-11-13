<?php

namespace Database\Seeders;

use App\Models\MainSlider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainSliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MainSlider::query()->create([
            'image' => url('/images/artist-slider/1.png'),
            'main_title' => 'هنرمندان محبوب',
            'sub_title' => 'متخصصان خدمات زیبایی',
            'description' => 'با هنرمندان محبوب خدمات خیلی خاص و همین طور تخفیفات ویژه برای کاربران دارد',
            'link_url' => 'https://beautymap.ir/artists',
            'link_title' => 'هنرمندان',
        ]);
    }
}
