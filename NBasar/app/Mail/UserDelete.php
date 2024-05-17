<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserDelete extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct(public $data)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Váš účet byl smazán',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'UserDeleted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
