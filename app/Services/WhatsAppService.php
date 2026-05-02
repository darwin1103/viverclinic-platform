<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected string $token;
    protected string $phoneId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token', '');
        $this->phoneId = config('services.whatsapp.phone_id', '');
    }

    /**
     * Send a WhatsApp template message.
     *
     * @param string $to
     * @param string $templateName
     * @param string $languageCode
     * @param array $components
     * @return array
     * @throws Exception
     */
    public function sendTemplateMessage(string $to, string $templateName, string $languageCode = 'es', array $components = []): array
    {
        $formattedPhone = $this->formatPhoneNumber($to);

        $url = "https://graph.facebook.com/v17.0/{$this->phoneId}/messages";

        $response = Http::withToken($this->token)->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $formattedPhone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ],
                'components' => $components
            ]
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp API Error', [
                'status' => $response->status(),
                'body' => $response->json(),
                'phone' => $formattedPhone
            ]);

            throw new Exception("WhatsApp API request failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Format the phone number to E.164 without the plus sign.
     * Assumes Colombia (+57) if 10 digits are provided without a country code.
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Assume Colombia (+57) if it is 10 digits
        if (strlen($phone) === 10) {
            $phone = '57' . $phone;
        }

        return $phone;
    }
}
