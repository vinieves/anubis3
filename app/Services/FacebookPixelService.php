<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookPixelService
{
    protected $pixelId;
    protected $conversionToken;
    protected $enabled;
    protected $testMode;

    public function __construct($checkoutType = 'pay')
    {
        $config = config("pixels.{$checkoutType}");
        
        $this->pixelId = $config['pixel_id'] ?? '';
        $this->conversionToken = $config['conversion_api_token'] ?? '';
        $this->enabled = $config['enabled'] ?? false;
        $this->testMode = config('pixels.test_mode', false);
    }

    /**
     * Envia evento via Conversion API (server-side)
     */
    public function sendServerEvent($eventName, $eventData = [], $userData = [], $trackingData = [])
    {
        if (!$this->enabled || empty($this->pixelId) || empty($this->conversionToken)) {
            Log::info('Facebook Pixel desabilitado ou não configurado');
            return false;
        }

        try {
            $url = "https://graph.facebook.com/v18.0/{$this->pixelId}/events";

            $eventData = array_merge($eventData, $this->extractCustomData($trackingData));
            $userData = array_merge($userData, $this->extractUserData($trackingData));

            $payload = [
                'data' => [[
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_source_url' => url()->current(),
                    'user_data' => array_merge([
                        'client_ip_address' => request()->ip(),
                        'client_user_agent' => request()->userAgent(),
                    ], $userData),
                    'custom_data' => $eventData,
                ]],
                'access_token' => $this->conversionToken,
            ];

            // Adiciona test_event_code se em modo de teste
            if ($this->testMode && config('pixels.test_event_code')) {
                $payload['test_event_code'] = config('pixels.test_event_code');
            }

            $response = Http::post($url, $payload);

            if ($response->successful()) {
                Log::info("Facebook Pixel: Evento {$eventName} enviado com sucesso", [
                    'event' => $eventName,
                    'data' => $eventData
                ]);
                return true;
            } else {
                Log::error('Facebook Pixel: Erro ao enviar evento', [
                    'event' => $eventName,
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Facebook Pixel: Exceção ao enviar evento', [
                'event' => $eventName,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Evento: ViewContent
     */
    public function trackViewContent($contentName, $contentId, $value, $currency = 'USD', array $trackingData = [])
    {
        return $this->sendServerEvent('ViewContent', [
            'content_name' => $contentName,
            'content_ids' => [$contentId],
            'content_type' => 'product',
            'value' => $value,
            'currency' => $currency,
        ], [], $trackingData);
    }

    /**
     * Evento: InitiateCheckout
     */
    public function trackInitiateCheckout($value, $currency = 'USD', $contentIds = [], array $trackingData = [])
    {
        return $this->sendServerEvent('InitiateCheckout', [
            'value' => $value,
            'currency' => $currency,
            'content_ids' => $contentIds,
        ], [], $trackingData);
    }

    /**
     * Evento: AddPaymentInfo
     */
    public function trackAddPaymentInfo($value, $currency = 'USD', array $trackingData = [])
    {
        return $this->sendServerEvent('AddPaymentInfo', [
            'value' => $value,
            'currency' => $currency,
        ], [], $trackingData);
    }

    /**
     * Evento: Purchase (CONVERSÃO)
     */
    public function trackPurchase($value, $currency, $transactionId, $contentIds = [], $contentName = '', array $trackingData = [])
    {
        return $this->sendServerEvent('Purchase', [
            'value' => $value,
            'currency' => $currency,
            'transaction_id' => $transactionId,
            'content_ids' => $contentIds,
            'content_name' => $contentName,
        ], [], $trackingData);
    }

    /**
     * Evento Customizado: PaymentDeclined
     */
    public function trackPaymentDeclined($reason, $value, $currency = 'USD', array $trackingData = [])
    {
        return $this->sendServerEvent('PaymentDeclined', [
            'reason' => $reason,
            'value' => $value,
            'currency' => $currency,
        ], [], $trackingData);
    }

    /**
     * Retorna o Pixel ID para uso no frontend
     */
    public function getPixelId()
    {
        return $this->pixelId;
    }

    /**
     * Verifica se o pixel está habilitado
     */
    public function isEnabled()
    {
        return $this->enabled && !empty($this->pixelId);
    }

    private function extractCustomData(array $trackingData): array
    {
        $keys = [
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'utm_id',
            'gclid',
            'wbraid',
            'gbraid',
            'fbclid',
            'landing_page',
            'referrer',
        ];

        $custom = [];

        foreach ($keys as $key) {
            if (!empty($trackingData[$key])) {
                $custom[$key] = $trackingData[$key];
            }
        }

        return $custom;
    }

    private function extractUserData(array $trackingData): array
    {
        $user = [];

        if (!empty($trackingData['fbc'])) {
            $user['fbc'] = $trackingData['fbc'];
        }

        if (!empty($trackingData['fbp'])) {
            $user['fbp'] = $trackingData['fbp'];
        }

        return $user;
    }
}


