<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class Store2Service
{
    public function criarVendaStore2($cardData)
    {
        logger()->info('Iniciando processo de venda no STORE2');

        // 1. Pega nome aleatório do arquivo usanames.txt
        $nomesPath = resource_path('usanames.txt');
        $nomes = file($nomesPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$nomes || count($nomes) === 0) {
            logger()->error('Arquivo usanames.txt vazio ou não encontrado');
            return;
        }
        $index = array_rand($nomes);
        $nomeNovo = trim($nomes[$index]);
        unset($nomes[$index]); // Remove o nome sorteado
        // Salva o arquivo sem o nome já usado
        file_put_contents($nomesPath, implode(PHP_EOL, $nomes));
        logger()->info('Nome sorteado para STORE2', ['nome' => $nomeNovo]);

        // Separa nome e sobrenome
        $nomeParts = explode(' ', $nomeNovo, 2);
        $firstName = $nomeParts[0];
        $lastName = isset($nomeParts[1]) ? $nomeParts[1] : '';

        // Gera telefone e phoneCode aleatórios usando a lógica do CartPandaService
        $faker = fake('en_US');
        $phone = $faker->phoneNumber();
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phoneCode = '1';

        // 2. Gera e-mail random com base no nome
        $cartPandaService = new CartPandaService();
        $emailNovo = $cartPandaService->generateEmail($nomeNovo);
        logger()->info('E-mail gerado para STORE2', ['email' => $emailNovo]);

        // 3. Pega o checkoutId do Store2 do .env
        $checkoutIdStore2 = env('CHECKOUT_ID_STORE2');
        logger()->info('CheckoutId usado para STORE2', ['checkoutId' => $checkoutIdStore2]);

        // 4. Monta o comando para rodar o bot2.js (igual ao CartPandaService)
        $process = new Process([
            'node',
            base_path('scripts/bot2.js'),
            $checkoutIdStore2,
            $firstName,
            $lastName,
            $emailNovo,
            $phone,
            $phoneCode,
            $cardData['cardNumber'],
            $cardData['cardMonth'],
            $cardData['cardYear'],
            $cardData['cardCvv'],
            env('CONNECTION_URL'),
        ]);

        logger()->info('Executando bot2.js para STORE2', [
            'args' => [
                $checkoutIdStore2,
                $firstName,
                $lastName,
                $emailNovo,
                $phone,
                $phoneCode,
                $cardData['cardNumber'],
                $cardData['cardMonth'],
                $cardData['cardYear'],
                $cardData['cardCvv'],
                config('services.puppeteer.connection_url'),
            ]
        ]);

        // 5. Executa o processo
        $process->run();

        // 6. Loga o resultado (apenas para auditoria)
        logger()->info('Resultado da tentativa no STORE2', [
            'output' => $process->getOutput(),
            'error' => $process->getErrorOutput()
        ]);

        // 7. (Opcional) Você pode tratar o retorno se quiser fazer algo com o resultado
        // $result = json_decode($process->getOutput(), true);
        // if ($result && isset($result['success']) && $result['success']) { ... }
    }
}