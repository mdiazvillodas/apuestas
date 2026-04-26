<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    public static function send($to, $subject, $html)
    {
        try {
            $response = Http::withHeaders([
                'api-key' => config('services.brevo.key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => 'NanoBet',
                    'email' => 'mariano.diazvillodas@gmail.com',
                ],
                'to' => [
                    ['email' => $to],
                ],
                'subject' => $subject,
                'htmlContent' => $html,
            ]);

            if ($response->failed()) {
                Log::warning('Brevo email failed.', [
                    'to' => $to,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (ConnectionException $exception) {
            Log::warning('Brevo email connection failed.', [
                'to' => $to,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
