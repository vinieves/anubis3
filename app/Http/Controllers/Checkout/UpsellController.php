<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Services\UpsellCartPandaService;
use Illuminate\Http\Request;

class UpsellController extends Controller
{
    private $upsellService;

    public function __construct(UpsellCartPandaService $upsellService)
    {
        $this->upsellService = $upsellService;
    }

    public function processUpsell(Request $request)
    {
        // Recupera os dados do pedido anterior da sessão
        $previousOrderData = [
            'firstName' => session('customer_name'),
            'lastName' => '', // Será preenchido abaixo
            'email' => session('customer_email'),
            'phone' => session('customer_phone'),
            'phoneCode' => '1',
            'cardNumber' => session('card_number'),
            'cardMonth' => session('card_month'),
            'cardYear' => session('card_year'),
            'cardCvv' => session('card_cvv')
        ];

        // Processa o nome completo para separar primeiro e último nome
        $fullName = $previousOrderData['firstName'];
        $nameParts = explode(' ', $fullName);
        $previousOrderData['firstName'] = $nameParts[0];
        array_shift($nameParts);
        $previousOrderData['lastName'] = implode(' ', $nameParts);

        // Pegar oferta atual da sessão para usar upsell específico
        $oferta = session('oferta_atual');
        $upsellId = $oferta['upsell1'] ?? env('CHECKOUT_ID2');
        
        // Usar o upsell específico da oferta
        $upsellService = new UpsellCartPandaService($upsellId);
        
        // Log para debug
        logger()->info('Processando upsell1', [
            'oferta_id' => $oferta['id'] ?? 'desconhecida',
            'upsell_id' => $upsellId
        ]);

        // Processa o upsell usando o serviço dedicado
        $result = $upsellService->processUpsell($previousOrderData);

        // Sempre redireciona para upsell2
        return response()->json([
            'success' => true,
            'redirect_url' => '/upsell2'
        ]);
    }
} 