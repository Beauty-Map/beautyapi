<?php

namespace Database\Seeders;

use App\Models\TicketSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'پشتیبانی مشتریان',
            'پشتیبانی بازاریابان',
            'پشتیبانی هنرمندان',
            'گزارش تخلف',
            'انتقادات و پیشنهادات',
            'درخواست همکاری',
        ];

        foreach ($subjects as $subject) {
            TicketSubject::query()->create([
                'title' => $subject
            ]);
        }
    }
}
