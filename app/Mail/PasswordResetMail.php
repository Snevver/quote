<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(string $resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password reset request',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderHtml(),
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function renderHtml(): string
    {
        $resetUrl = e($this->resetUrl);
        $requestedAt = now()->format('Y-m-d H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a0a0a;
            color: #e0e0e0;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #111;
            border: 1px solid #333;
            padding: 30px;
        }
        h1 {
            font-size: 18px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            color: #ddd;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 18px;
            background-color: #fff;
            color: #000 !important;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
            font-weight: 700;
        }
        .link {
            word-break: break-all;
            color: #8ab4ff;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #222;
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Access</h1>

        <p>A password reset was requested for the admin account. Use the button below to open the Vue reset page and choose a new password.</p>

        <p>
            <a class="button" href="{$resetUrl}">Reset password</a>
        </p>

        <p>If the button does not work, copy this link into your browser:</p>
        <p class="link">{$resetUrl}</p>

        <div class="footer">
            Requested at {$requestedAt} UTC
        </div>
    </div>
</body>
</html>
HTML;
    }
}
