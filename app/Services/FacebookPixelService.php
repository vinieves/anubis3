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
    public function sendServerEvent($eventName, $eventData = [], $userData = [])
    {
        if (!$this->enabled || empty($this->pixelId) || empty($this->conversionToken)) {
            Log::info('Facebook Pixel desabilitado ou não configurado');
            return false;
        }

        try {
            $url = "https://graph.facebook.com/v18.0/{$this->pixelId}/events";

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
    public function trackViewContent($contentName, $contentId, $value, $currency = 'USD')
    {
        return $this->sendServerEvent('ViewContent', [
            'content_name' => $contentName,
            'content_ids' => [$contentId],
            'content_type' => 'product',
            'value' => $value,
            'currency' => $currency,
        ]);
    }

    /**
     * Evento: InitiateCheckout
     */
    public function trackInitiateCheckout($value, $currency = 'USD', $contentIds = [])
    {
        return $this->sendServerEvent('InitiateCheckout', [
            'value' => $value,
            'currency' => $currency,
            'content_ids' => $contentIds,
        ]);
    }

    /**
     * Evento: AddPaymentInfo
     */
    public function trackAddPaymentInfo($value, $currency = 'USD')
    {
        return $this->sendServerEvent('AddPaymentInfo', [
            'value' => $value,
            'currency' => $currency,
        ]);
    }

    /**
     * Evento: Purchase (CONVERSÃO)
     */
    public function trackPurchase($value, $currency, $transactionId, $contentIds = [], $contentName = '')
    {
        return $this->sendServerEvent('Purchase', [
            'value' => $value,
            'currency' => $currency,
            'transaction_id' => $transactionId,
            'content_ids' => $contentIds,
            'content_name' => $contentName,
        ]);
    }

    /**
     * Evento Customizado: PaymentDeclined
     */
    public function trackPaymentDeclined($reason, $value, $currency = 'USD')
    {
        return $this->sendServerEvent('PaymentDeclined', [
            'reason' => $reason,
            'value' => $value,
            'currency' => $currency,
        ]);
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
}

