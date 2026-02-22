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
use App\Models\MailRecord;
use App\Models\User;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmailMailerChat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataSendMail;
    /**
     * Create a new job instance.
     */
    public function __construct($dataSendMail)
    {
        $this->dataSendMail = $dataSendMail;
    }

    /**
     * Execute the job.
     */
    public $timeout = 60;
    public $tries = 3;

    public function handle(): void
    {
        $isipesan = $this->dataSendMail['message'];
        $judul = $this->dataSendMail['title'];
        $institusi = $this->dataSendMail['institusi'];
        $session = $this->dataSendMail['session'];
        $mailSetting = $this->dataSendMail['MailSetting'];
        $receiver = $this->dataSendMail['receiver'];

        $debugLog = '';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $mailSetting['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailSetting['user'];
        $mail->Password = $mailSetting['pass']; // ganti dengan app password valid
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailSetting['port'];

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) use (&$debugLog) {
            $debugLog .= '<span style="display:block;color:black;">' . gmdate('Y-m-d H:i:s') . " [{$level}] " . htmlspecialchars($str) . "</span>\n";
        };

        $mail->setFrom($mailSetting['user'], 'MyPartnership UMS');
        $mail->addReplyTo($mailSetting['reply_to'], $mailSetting['subjek_reply_to']);
        if ($session == 'dev') {
            $mail->addAddress('mtzal128@gmail.com'); // dinamis per email
        } else {
            $mail->addAddress($receiver); // dinamis per email
        }

        $mail->Subject = $judul;
        $mail->isHTML(true);
        $mail->Body = $isipesan;

        try {
            $mail->send();

            MailRecord::create([
                'status_sent' => 'Sukses',
                'subject_sent' => $mail->Subject,
                'pesan_sent' => $mail->Body,
                'institusi' => $institusi,
                'send_to' => $receiver,
                'email_from' => $mailSetting['user'],
                'debug_error' => '<pre><br><strong>Berhasil kirim:</strong>' . htmlspecialchars($mail->ErrorInfo) . '<br>' . $debugLog . '</pre>',
                'tanggal_sent' => now(),
                'created_at' => now(),
            ]);
        } catch (Exception $e) {
            MailRecord::create([
                'status_sent' => 'Gagal',
                'subject_sent' => $mail->Subject,
                'pesan_sent' => $mail->Body,
                'institusi' => $institusi,
                'send_to' => $receiver,
                'email_from' => $mailSetting['user'],
                'debug_error' => '<pre><br><strong>Gagal kirim:</strong> ' . htmlspecialchars($mail->ErrorInfo) . '<br>' . $debugLog . '</pre>',
                'tanggal_sent' => now(),
                'created_at' => now(),
            ]);
        }

        // Reset PHPMailer untuk pengiriman berikutnya
        $mail->clearAddresses();
        $mail->clearReplyTos();

        // try {
        //     if ($this->session == 'dev') {
        //         Mail::to('mtzal128@gmail.com')->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
        //     } else {
        //         // Mail::to('mtzal128@gmail.com')->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
        //         Mail::to($this->receiver)->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
        //     }
        //     // Success Log
        //     EmailLog::create([
        //         'sender' => $dataMessage['sender'],
        //         'email' => $this->receiver,
        //         'subject' => $this->title,
        //         'message' => $this->message,
        //         'success' => true,
        //     ]);
        // } catch (\Throwable $e) {
        //     // Gagal mengirim email
        //     EmailLog::create([
        //         'sender' => $dataMessage['sender'],
        //         'email' => $this->receiver,
        //         'subject' => $this->title,
        //         'message' => $this->message,
        //         'success' => false,
        //         'error' => $e->getMessage(),
        //     ]);

        //     // Optional log file
        //     \Log::error("Gagal kirim email ke {$this->receiver}: " . $e->getMessage());
        // }
        // }
    }
}
