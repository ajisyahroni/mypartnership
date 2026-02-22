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

class SendReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $title;
    protected $receiver;
    protected $sender;
    protected $session;
    /**
     * Create a new job instance.
     */
    public function __construct($message, $title, $receiver, $sender, $session)
    {
        $this->message = $message;
        $this->title = $title;
        $this->receiver = $receiver;
        $this->sender = $sender;
        $this->session = $session;
    }

    /**
     * Execute the job.
     */
    public $timeout = 60;
    public $tries = 3;

    public function handle(): void
    {

        $dataMessage = [
            'message' => $this->message,
            'sender' => $this->sender,
            'subject' => $this->title,
        ];

        // Kirim email
        // foreach ($arrReceiver as $email) {
        try {
            if ($this->session == 'dev') {
                Mail::to('mtzal128@gmail.com')->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
            } else {
                // Mail::to('mtzal128@gmail.com')->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
                Mail::to($this->receiver)->send(new PengirimanEmail($this->message, $dataMessage['sender'], $this->title));
            }
            // Success Log
            EmailLog::create([
                'sender' => $dataMessage['sender'],
                'email' => $this->receiver,
                'subject' => $this->title,
                'message' => $this->message,
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            // Gagal mengirim email
            EmailLog::create([
                'sender' => $dataMessage['sender'],
                'email' => $this->receiver,
                'subject' => $this->title,
                'message' => $this->message,
                'success' => false,
                'error' => $e->getMessage(),
            ]);

            // Optional log file
            \Log::error("Gagal kirim email ke {$this->receiver}: " . $e->getMessage());
        }
    }
}
