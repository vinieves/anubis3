<?php

namespace App\Http\Controllers\Pay;

use App\Http\Controllers\Controller;
use App\Services\UpsellCartPandaService;
use App\Services\FacebookPixelService;
use Illuminate\Http\Request;

class PayUpsellController extends Controller
{
    protected $pixelService;

    public function __construct()
    {
        $this->pixelService = new FacebookPixelService('pay');
    }

    public function index()
    {
        $oferta = session('pay_oferta_atual');
        
        return view('pay.upsell1', [
            'oferta' => $oferta,
            'pixelId' => $this->pixelService->getPixelId(),
            'pixelEnabled' => $this->pixelService->isEnabled(),
            'trackingData' => session('pay_tracking', []),
        ]);
    }

    public function processUpsell(Request $request)
    {
        // Recupera os dados do pedido anterior da sessão
        $previousOrderData = [
            'firstName' => session('pay_customer_name'),
            'lastName' => '',
            'email' => session('pay_customer_email'),
            'phone' => session('pay_customer_phone'),
            'phoneCode' => '1',
            'cardNumber' => session('pay_card_number'),
            'cardMonth' => session('pay_card_month'),
            'cardYear' => session('pay_card_year'),
            'cardCvv' => session('pay_card_cvv')
        ];

        // Processa o nome completo
        $fullName = $previousOrderData['firstName'];
        $nameParts = explode(' ', $fullName);
        $previousOrderData['firstName'] = $nameParts[0];
        array_shift($nameParts);
        $previousOrderData['lastName'] = implode(' ', $nameParts);

        // Pegar oferta atual da sessão para usar upsell específico
        $oferta = session('pay_oferta_atual');
        $upsellId = $oferta['upsell1'] ?? env('CHECKOUT_ID2');
        
        // Usar o upsell específico da oferta
        $upsellService = new UpsellCartPandaService($upsellId);
        
        logger()->info('[PAY] Processando upsell1', [
            'oferta_id' => $oferta['id'] ?? 'desconhecida',
            'upsell_id' => $upsellId
        ]);

        // Processa o upsell
        $result = $upsellService->processUpsell($previousOrderData);

        // Tracking: Se aceitar o upsell, dispara Purchase
        // (Pode adicionar lógica de tracking aqui se necessário)

        // Sempre redireciona para upsell2 do pay
        return response()->json([
            'success' => true,
            'redirect_url' => '/pay/upsell2'
        ]);
    }
}

