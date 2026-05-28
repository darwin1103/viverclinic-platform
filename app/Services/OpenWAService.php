<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenWAService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $sessionId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.openwa.base_url', 'http://localhost:2785'), '/');
        $this->apiKey = config('services.openwa.api_key', '');
        $this->sessionId = config('services.openwa.session_id', 'default');
    }

    /**
     * Send a text message using OpenWA.
     *
     * @param string $to
     * @param string $text
     * @return array
     * @throws Exception
     */
    public function sendTextMessage(string $to, string $text): array
    {
        $chatId = $this->formatPhoneNumber($to);
        $url = "{$this->baseUrl}/api/sessions/{$this->sessionId}/messages/send-text";

        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, [
            'chatId' => $chatId,
            'text' => $text,
        ]);

        if ($response->failed()) {
            Log::error('OpenWA API Error', [
                'status' => $response->status(),
                'body' => $response->json(),
                'chatId' => $chatId,
            ]);

            throw new Exception("OpenWA API request failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Format the phone number to chatId format.
     * Assumes Colombia (+57) if 10 digits are provided without a country code.
     *
     * @param string $phone
     * @return string
     */
    public function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Assume Colombia (+57) if it is 10 digits
        if (strlen($phone) === 10) {
            $phone = '57' . $phone;
        }

        return $phone . '@c.us';
    }
}
