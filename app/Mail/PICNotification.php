<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PICNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $dataMessage;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($dataMessage, $subject)
    {
        $this->dataMessage = $dataMessage;
        $this->subject = $subject;
    }


    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.template')
            ->with([
                'message' => $this->dataMessage,
            ]);
    }
}
