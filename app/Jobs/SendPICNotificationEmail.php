<?php

namespace App\Jobs;

use App\Mail\PICNotification;
use App\Models\MailSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;


class SendPICNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $role;
    protected $session;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $role, $session)
    {
        $this->data = $data;
        $this->role = $role;
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public $timeout = 60;
    public $tries = 3;

    public function handle(): void
    {
        $mail = MailSetting::where('is_active', '1')->first();

        $subjek = $mail->subjek_pic_kegiatan;
        $viewEmail = $mail->pic_kegiatan;

        $judul = $this->data['judul'] ?? ($this->data['judul_lain'] ?? '-');
        $subjek = str_replace("{@nama_kegiatan}", $judul, $subjek);
        $message = str_replace("{@nama_kegiatan}", $judul, $viewEmail);

        if (empty(trim($message))) {
            $message = "Tidak ada pesan yang tersedia.";
        }

        $dataMessage = [
            'message' => $message,
            'sender' => $this->data['sender'] ?? 'Sistem',
            'subject' => $subjek
        ];

        $receivers = (array) ($this->data['pic_kegiatan'] ?? []);

        foreach ($receivers as $email) {
            try {
                if ($this->session == 'dev') {
                    Mail::to($mail->email_receiver)->send(new PICNotification($dataMessage, $subjek));
                } else {
                    Mail::to($email)->send(new PICNotification($dataMessage, $subjek));
                }


                // Success Log
                EmailLog::create([
                    'sender' => $dataMessage['sender'],
                    'email' => $email,
                    'subject' => $subjek,
                    'message' => $message,
                    'success' => true,
                ]);
            } catch (\Exception $e) {
                // Failed Log
                EmailLog::create([
                    'sender' => $dataMessage['sender'],
                    'email' => $email,
                    'subject' => $subjek,
                    'message' => $message,
                    'success' => false,
                    'error' => $e->getMessage(),
                ]);

                // Optional log file
                \Log::error("Gagal kirim email ke {$email}: " . $e->getMessage());
            }
        }
    }
}
