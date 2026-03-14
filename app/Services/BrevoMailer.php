<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailer
{
    public static function send($to, $subject, $html)
    {
        Http::withHeaders([
            'api-key' => config('services.brevo.key'),
            'Content-Type' => 'application/json'
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => 'NanoBet',
                'email' => 'no-reply@nanobet.app'
            ],
            'to' => [
                ['email' => $to]
            ],
            'subject' => $subject,
            'htmlContent' => $html
        ]);
    }
}