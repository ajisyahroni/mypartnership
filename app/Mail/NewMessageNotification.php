<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $chatMessage;
    public $sender;

    /**
     * Create a new message instance.
     */
    public function __construct($chatMessage, $sender)
    {
        $this->chatMessage = $chatMessage;
        $this->sender = $sender;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Pesan Baru Dari ' . $this->sender->name)
            ->view('emails.new_message')
            ->with([
                'chatMessage' => $this->chatMessage,
                'sender' => $this->sender,
            ]);
    }
    // public function build()
    // {
    //     return $this->from('noreply@yourdomain.com', 'MyPartnership') // Set email sender
    //         ->subject('Pesan Baru Dari ' . ucwords($this->sender->name))
    //         ->view('emails.new_message')
    //         ->with([
    //             'chatMessage' => $this->chatMessage,
    //             'sender' => $this->sender,
    //         ]);
    // }
}
