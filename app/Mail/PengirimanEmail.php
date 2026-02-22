<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PengirimanEmail extends Mailable
{
    public $dataMessage;
    public $sender;
    public $subject;

    public function __construct($dataMessage, $sender, $subject)
    {
        // Assign dataMessage, sender, and subject to the public properties
        $this->dataMessage = $dataMessage;
        $this->sender = $sender;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.template') // View email template
            ->with([
                'dataMessage' => $this->dataMessage,  // Pass data to template
            ]);
    }
}
