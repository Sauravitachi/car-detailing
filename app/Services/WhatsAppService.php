<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService {
    protected string $token;
    protected string $phoneId;

    public function __construct() {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
    }

    public function sendCheckinLink(string $phone, string $customerName, string $vehicleNumber, string $token): bool {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $formattedPhone = str_starts_with($cleanPhone, '91') ? $cleanPhone : '91' . $cleanPhone;
        $trackingUrl = url("/track/{$token}");

        $response = Http::withToken($this->token)->post(
            "https://graph.facebook.com/v19.0/{$this->phoneId}/messages",
            [
                'messaging_product' => 'whatsapp',
                'to' => $formattedPhone,
                'type' => 'text',
                'text' => [
                    'body' => "Namaste {$customerName}, aapki car ({$vehicleNumber}) ka check-in ho chuka hai.\n\nPre-existing scratches aur live progress dekhne ke liye yahan tap karein:\n{$trackingUrl}"
                ]
            ]
        );

        if ($response->failed()) {
            Log::error('WhatsApp Dispatch Failed', $response->json());
            return false;
        }

        return true;
    }
}
?>