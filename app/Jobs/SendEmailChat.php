<?php

namespace App\Jobs;

use App\Mail\NewMessageNotification;
use App\Mail\PengirimanEmail;
use App\Mail\PICNotification;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;
use App\Models\User;

class SendEmailChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sender;
    protected $message;
    protected $receiver;
    protected $session;
    /**
     * Create a new job instance.
     */
    public function __construct($sender, $message, $receiver, $session)
    {
        $this->sender = $sender;
        $this->message = $message;
        $this->receiver = $receiver;
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

        $sender = $this->sender;

        if ($this->session == 'dev') {
            $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
        } else {
            $arrReceiver = [$this->receiver];
        }


        // $institusiReplace = (string) str_replace("{@nama_institusi}", $this->data['nama_institusi'], $viewEmail);
        // $statusReplace = (string) str_replace("{@status}", $this->status == '1' ? 'Verifikasi' : 'Batalkan Verifikasi', $institusiReplace);
        // $message = (string) str_replace("{@verifikator}", $this->role == 'user' ? 'Pengusul' : ucwords($this->role), $statusReplace);

        // Jika $message kosong, beri fallback default
        if (empty(trim($this->message))) {
            $message = "Tidak ada pesan yang tersedia.";
        }

        // Kirim email
        foreach ($arrReceiver as $email) {
            try {
                Mail::to($email)->send(new NewMessageNotification($this->message, $this->sender));

                // Success Log
                EmailLog::create([
                    'sender' => $this->sender,
                    'email' => $email,
                    'subject' => 'Pesan Baru dari ' . $sender,
                    'message' => $message,
                    'success' => true,
                ]);
            } catch (\Throwable $e) {
                // Gagal mengirim email
                EmailLog::create([
                    'sender' => $this->sender,
                    'email' => $email,
                    'subject' => 'Pesan Baru dari ' . $sender,
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
