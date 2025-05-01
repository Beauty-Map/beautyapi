<?php
namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Morilog\Jalali\Jalalian;

class ArtistsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return User::whereHas('roles', function ($q) {
            $q->where('name', 'artist');
        })
            ->with(['city.province', 'portfolios', 'metas'])
            ->get()
            ->map(/**
             * @param User $user
             * @return array
             */ function (User $user) {
                return [
                    'نام' => $user->getMeta('first_name') ?? explode(' ', $user->full_name)[0] ?? '',
                    'نام خانوادگی' => $user->getMeta('last_name') ?? explode(' ', $user->full_name)[1] ?? '',
                    'تاریخ عضویت' => Jalalian::forge($user->created_at)->format('Y/m/d'),
                    'نام کاربری' => $user->getMeta('username') ?? '-',
                    'تلفن همراه' => $user->phone_number,
                    'استان' => optional($user->city->province)->name ?? '-',
                    'شهرستان' => optional($user->city)->name ?? '-',
                    'پنل خریداری شده' => $user->getSelectedPlan()->plan->title ?? '-',
                    'تاریخ شروع' => optional($user->getSelectedPlan())->start_date
                        ? Jalalian::forge($user->getSelectedPlan()->start_date)->format('Y/m/d')
                        : '-',
                    'تاریخ انقضا پنل' => optional($user->getSelectedPlan())->expire_date
                        ? Jalalian::forge($user->getSelectedPlan()->expire_date)->format('Y/m/d')
                        : '-',
                    'تعداد نمونه کار ثبت شده' => $user->portfolios()->count(),
                    'تعداد نردبان نمونه کارها' => $user->getMeta('ladder_count') ?? 0,
                    'سکه‌های موجود' => $user->getCoins(),
                    'تاریخ آخرین ورود به پنل' => $user->getMeta('last_login_at')
                        ? Jalalian::forge($user->getMeta('last_login_at'))->format('Y/m/d H:i')
                        : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'نام',
            'نام خانوادگی',
            'تاریخ عضویت',
            'نام کاربری',
            'تلفن همراه',
            'استان',
            'شهرستان',
            'پنل خریداری شده',
            'تاریخ شروع',
            'تاریخ انقضا پنل',
            'تعداد نمونه کار ثبت شده',
            'تعداد نردبان نمونه کارها',
            'سکه‌های موجود',
            'تاریخ آخرین ورود به پنل',
        ];
    }
}
