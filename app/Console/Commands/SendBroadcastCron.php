<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SinkronasiController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendBroadcastCron extends Command
{
    protected $signature = 'broadcast:daily';
    protected $description = 'Mengirim email reminder 6 bulan sebelum expired (harian) dan Mengirim email produktif Kerja Sama';

    public function handle()
    {
        $controller = new ReminderController();
        $controller->SendBroadcastCron(request());
        // \Artisan::call('queue:work', ['--once' => true]);

        $this->info('Broadcast Cron (Daily) Berhasil Dijalankan');
    }
}
