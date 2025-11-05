<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Upsell2CartPandaService
{
    public $checkoutId3;

    public function __construct($checkoutId3 = null)
    {
        $this->checkoutId3 = $checkoutId3 ?? env('CHECKOUT_ID3');
    }

    public function processUpsell2($previousOrderData): array
    {
        set_time_limit(120);

        try {
            // Log dos dados que serão reutilizados
            logger()->info('Dados do cliente para upsell2', [
                'firstName' => $previousOrderData['firstName'],
                'lastName' => $previousOrderData['lastName'],
                'email' => $previousOrderData['email'],
                'phone' => $previousOrderData['phone'],
                'cardNumber' => substr($previousOrderData['cardNumber'], -4) // Log apenas últimos 4 dígitos
            ]);

            // Usa exatamente o mesmo script do primeiro checkout, apenas com CHECKOUT_ID3
            $process = new Process([
                'node',
                '../scripts/bot.js', // Mesmo script do primeiro checkout
                $this->checkoutId3,
                $previousOrderData['firstName'],
                $previousOrderData['lastName'],
                $previousOrderData['email'],
                $previousOrderData['phone'],
                $previousOrderData['phoneCode'],
                $previousOrderData['cardNumber'],
                $previousOrderData['cardMonth'],
                $previousOrderData['cardYear'],
                $previousOrderData['cardCvv'],
                config('services.puppeteer.connection_url')
            ]);

            $process->setTimeout(120); // Aumenta o timeout para 2 minutos
            $process->run();

            // Log de erros do processo se houver
            if (! $process->isSuccessful()) {
                logger()->error('Processo do upsell2 falhou', ['error' => $process->getErrorOutput()]);
                throw new \Exception('Processo do upsell2 falhou: ' . $process->getErrorOutput());
            }

            $output = $process->getOutput();
            $errors = $process->getErrorOutput();

            // Log de erros se houver
            if (! empty($errors)) {
                logger()->error('Erro ao criar pedido do upsell2', ['errors' => $errors]);
                throw new \Exception('Erro ao criar pedido do upsell2: ' . $errors);
            }

            // Log antes de decodificar o JSON
            logger()->info('Output bruto do bot (upsell2)', ['output' => $output]);

            // Tenta ajustar o formato do JSON
            $outputAjustado = $this->ajustarFormatoJson($output);

            // Tenta decodificar o JSON
            try {
                $result = json_decode($outputAjustado, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    logger()->info('Resultado do upsell2 processado', ['result' => $result]);

                    // Se tiver erro no resultado do bot, lança exceção
                    if (isset($result['error']) && $result['error'] === true) {
                        throw new \Exception($result['message'] ?? 'Erro no processamento do upsell2');
                    }

                    // Verifica se o pagamento foi recusado por falta de saldo
                    if (isset($result['message']) && $result['message'] === 'Payment declined. Try another card or payment method.') {
                        logger()->info('Cliente sem saldo, redirecionando para /thankyou');
                    }

                    return [
                        'success' => true,
                        'redirect_url' => '/thankyou',
                        'payment_status' => $result['payment_status'] ?? 'Pagamento processado com sucesso'
                    ];
                }
            } catch (\Exception $e) {
                logger()->error('Erro ao processar resultado do upsell2', ['error' => $e->getMessage()]);
                throw $e;
            }

        } catch (\Exception $e) {
            logger()->error('Erro no processo do upsell2', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erro ao processar o pagamento: ' . $e->getMessage()
            ];
        }

        // Fallback em caso de erro não tratado
        return [
            'success' => false,
            'message' => 'Erro não esperado ao processar o pagamento'
        ];
    }

    // Função para ajustar o formato do JSON
    function ajustarFormatoJson($output) {
        // Substitui aspas simples por aspas duplas
        $output = str_replace("'", '"', $output);

        // Adiciona aspas duplas em torno das chaves
        $output = preg_replace('/(\w+):/', '"$1":', $output);

        return $output;
    }
} 