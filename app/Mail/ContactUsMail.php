<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $subjectText,
        public string $contactMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(
                    $this->email,
                    $this->name
                ),
            ],
            subject: 'New Contact Message - ' . $this->subjectText,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
