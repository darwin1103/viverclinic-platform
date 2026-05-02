<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudService
{
    protected $token;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.cloud_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    public function sendMessage($to, $templateName, $languageCode = 'es')
    {
        try {
            $url = "https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $languageCode
                    ]
                ]
            ];

            $response = Http::withToken($this->token)->post($url, $payload);

            if ($response->failed()) {
                Log::error('WhatsApp API Error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'payload' => $payload
                ]);
            }

            return $response->json();
            
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception', [
                'message' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
