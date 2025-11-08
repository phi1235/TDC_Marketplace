<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build()
    {
        $subject = '[TDC Contact] ' . ($this->payload['subject'] ?? 'Liên hệ mới');
        return $this->subject($subject)
            ->markdown('emails.contact_message');
    }
}
