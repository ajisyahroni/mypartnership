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

class SendEmailVerifikasiPengajuan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $role;
    protected $status;
    protected $session;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $role, $status, $session)
    {
        $this->data = $data;
        $this->role = $role;
        $this->status = $status;
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

        $subjek = $mail->subjek_verifikasi;
        $viewEmail = $mail->verifikasi;
        $sender = $this->data['sender'];

        if ($this->session == 'dev') {
            $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
        } else {
            if ($this->role == 'verifikator' || $this->role == 'user') {
                // Kirim Ke Admin
                // $sender = Auth::user()->name;
                $arrReceiver = array_map('trim', explode(',', $mail->email_receiver));
            } else if ($this->role == 'admin') {
                // Kirim Ke User
                // $sender = 'Admin';
                $arrReceiver = User::where('username', $this->data['add_by'])
                    ->distinct()
                    ->pluck('email')
                    ->toArray();
            }
        }


        $institusiReplace = (string) str_replace("{@nama_institusi}", $this->data['nama_institusi'], $viewEmail);
        $statusReplace = (string) str_replace("{@status}", $this->status == '1' ? 'Verifikasi' : 'Batalkan Verifikasi', $institusiReplace);
        $message = (string) str_replace("{@verifikator}", $this->role == 'user' ? 'Pengusul' : ucwords($this->role), $statusReplace);

        // Jika $message kosong, beri fallback default
        if (empty(trim($message))) {
            $message = "Tidak ada pesan yang tersedia.";
        }

        // Ambil data pengirim & penerima

        $dataMessage = [
            'message' => $message,
            'sender' => $sender,
            'subject' => $subjek
        ];

        // Kirim email
        foreach ($arrReceiver as $email) {
            try {
                Mail::to($email)->send(new PengirimanEmail($message,  $sender, $subjek));

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
