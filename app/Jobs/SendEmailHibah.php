<?php

namespace App\Jobs;

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

class SendEmailHibah implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $session;
    /**
     * Create a new job instance.
     */
    public function __construct($data,  $session)
    {
        $this->data = $data;
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

        if ($this->tipe == 'edit' && $this->data['stats_kerma'] == 'Ajuan Baru') {
            $subjek = $mail->subjek_edit_ajuan;
            $viewEmail = $mail->ajuan_edit_email;
        } else if ($this->tipe == 'submit' && $this->data['stats_kerma'] == 'Ajuan Baru') {
            $subjek = $mail->subjek_ajuan;
            $viewEmail = $mail->ajuan_email;
        } else if ($this->tipe == 'edit' && $this->data['stats_kerma'] == 'Lapor Kerma') {
            $subjek = $mail->subjek_edit_lapor;
            $viewEmail = $mail->lapor_edit_email;
        } else if ($this->tipe == 'submit' && $this->data['stats_kerma'] == 'Lapor Kerma') {
            $subjek = $mail->subjek_lapor;
            $viewEmail = $mail->lapor_email;
        }

        if ($this->session == 'dev') {
            $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
        } else {
            if ($this->receiver == 'admin') {
                $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
            } else {
                $arrReceiver = User::where('place_state', $this->data['place_state'])
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'verifikator');
                    })
                    ->distinct()
                    ->pluck('email')
                    ->toArray();
            }
        }

        $message = (string) str_replace("{@nama_institusi}", $this->data['nama_institusi'], $viewEmail);

        // Jika $message kosong, beri fallback default
        if (empty(trim($message))) {
            $message = "Tidak ada pesan yang tersedia.";
        }

        // Ambil data pengirim & penerima
        $sender = $this->data['sender'];


        $dataMessage = [
            'message' => $message,
            'sender' => $sender,
            'subject' => $subjek
        ];

        // // Kirim email
        foreach ($arrReceiver as $email) {
            try {
                Mail::to($email)->send(new PengirimanEmail($dataMessage, $sender));
                // Success Log
                EmailLog::create([
                    'sender' => $dataMessage['sender'],
                    'email' => $email,
                    'subject' => $subjek,
                    'message' => $message,
                    'success' => true,
                ]);
            } catch (\Throwable $e) {
                // Gagal mengirim email
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
