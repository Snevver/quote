<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginPageVisited extends Mailable
{
    use Queueable, SerializesModels;

    public string $ip;

    public string $userAgent;

    public string $timestamp;

    public function __construct(string $ip, string $userAgent)
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->timestamp = now()->format('F j, Y \a\t g:i A');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login page visited',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login_page_visited',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
