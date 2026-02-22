<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Penjadwalan harian
Schedule::command('broadcast:daily')->dailyAt('08:00');
// Schedule::command('broadcast:daily')->dailyAt('14:46');

// Penjadwalan bulanan setiap tanggal 1 pukul 08:00
// Schedule::command('broadcast:monthly')->monthlyOn(1, '08:00');
