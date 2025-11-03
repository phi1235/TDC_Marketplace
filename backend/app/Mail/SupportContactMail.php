<?php

// app/Mail/SupportContactMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build()
    {
        return $this->subject('[TDC Marketplace] Yêu cầu hỗ trợ mới')
            ->view('emails.support_contact'); // tạo view bên dưới
    }
}

