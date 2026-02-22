<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReminderController;
use Illuminate\Console\Command;

class SendBroadcastCronMonth extends Command
{
    protected $signature = 'broadcast:monthly';
    protected $description = 'Mengirim email reminder data expired dari sekarang hingga 6 bulan ke depan (bulanan)';

    public function handle()
    {
        $controller = new ReminderController();
        $controller->SendBroadcastCronMonth(request());
        // \Artisan::call('queue:work', ['--once' => true]);
        $this->info('Broadcast Cron (Monthly) Berhasil Dijalankan');
    }
}
