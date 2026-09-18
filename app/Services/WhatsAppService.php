<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WhatsAppService
{
    protected ?string $token;

    protected ?string $phoneId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
    }

    public function sendCheckinLink(string $phone, string $customerName, string $vehicleNumber, string $trackingToken): bool
    {
        if (! $this->token || ! $this->phoneId) {
            Log::warning('WhatsApp is not configured; check-in link was not dispatched.');

            return false;
        }

        $cleanPhone = preg_replace('/\D+/', '', $phone);
        $formattedPhone = str_starts_with($cleanPhone, '91') ? $cleanPhone : '91'.$cleanPhone;
        $trackingUrl = route('track', $trackingToken);

        try {
            $response = Http::withToken($this->token)
                ->connectTimeout(5)
                ->timeout(10)
                ->post(config('services.whatsapp.endpoint'), [
                    'messaging_product' => 'whatsapp',
                    'to' => $formattedPhone,
                    'type' => 'text',
                    'text' => [
                        'body' => "Namaste {$customerName}, aapki car ({$vehicleNumber}) ka check-in ho chuka hai.\n\nPre-existing scratches aur live progress dekhne ke liye yahan tap karein:\n{$trackingUrl}",
                    ],
                ]);
        } catch (Throwable $exception) {
            Log::error('WhatsApp dispatch threw an exception.', ['message' => $exception->getMessage()]);

            return false;
        }

        if ($response->failed()) {
            Log::error('WhatsApp dispatch failed.', ['response' => $response->json()]);

            return false;
        }

        return true;
    }
}
