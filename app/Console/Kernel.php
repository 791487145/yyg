<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\Order::class,
        \App\Console\Commands\Goods::class,
        \App\Console\Commands\Temp::class,
        \App\Console\Commands\GenerateQrCode::class,
        \App\Console\Commands\UpdateCoupon::class,
        \App\Console\Commands\UpdateGuideTa::class,
        \App\Console\Commands\OrderCallPay::class,
        \App\Console\Commands\ConfBanners::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}
