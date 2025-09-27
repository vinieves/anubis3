<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuração de Ofertas Dinâmicas
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações para o sistema de ofertas dinâmicas.
    | Cada oferta possui seu próprio checkout ID e configurações de upsell.
    |
    */

    'ofertas' => [
        'oferta1' => [
            'id' => 'oferta1',
            'checkout_id' => env('OFERTA1_CHECKOUT', env('CHECKOUT_ID')),
            'nome' => env('OFERTA1_NOME', 'ProsperityTone - App'),
            'preco' => env('OFERTA1_PRECO', '9.92'),
            'descricao' => env('OFERTA1_DESCRICAO', 'Biblical Healing Frequency'),
            'upsell1' => env('OFERTA1_UPSELL1', env('CHECKOUT_ID2')),
            'upsell2' => env('OFERTA1_UPSELL2', env('CHECKOUT_ID3')),
        ],
        'oferta2' => [
            'id' => 'oferta2',
            'checkout_id' => env('OFERTA2_CHECKOUT', env('CHECKOUT_ID')),
            'nome' => env('OFERTA2_NOME', 'Healing Frequencies'),
            'preco' => env('OFERTA2_PRECO', '7.99'),
            'descricao' => env('OFERTA2_DESCRICAO', 'Sound Therapy App'),
            'upsell1' => env('OFERTA2_UPSELL1', env('CHECKOUT_ID2')),
            'upsell2' => env('OFERTA2_UPSELL2', env('CHECKOUT_ID3')),
        ],
        'oferta3' => [
            'id' => 'oferta3',
            'checkout_id' => env('OFERTA3_CHECKOUT', env('CHECKOUT_ID')),
            'nome' => env('OFERTA3_NOME', 'Bible Sounds'),
            'preco' => env('OFERTA3_PRECO', '12.50'),
            'descricao' => env('OFERTA3_DESCRICAO', 'Sacred Audio Collection'),
            'upsell1' => env('OFERTA3_UPSELL1', env('CHECKOUT_ID2')),
            'upsell2' => env('OFERTA3_UPSELL2', env('CHECKOUT_ID3')),
        ],
        'oferta4' => [
            'id' => 'oferta4',
            'checkout_id' => env('OFERTA4_CHECKOUT', env('CHECKOUT_ID')),
            'nome' => env('OFERTA4_NOME', 'Prayer Music'),
            'preco' => env('OFERTA4_PRECO', '15.99'),
            'descricao' => env('OFERTA4_DESCRICAO', 'Divine Worship Collection'),
            'upsell1' => env('OFERTA4_UPSELL1', env('CHECKOUT_ID2')),
            'upsell2' => env('OFERTA4_UPSELL2', env('CHECKOUT_ID3')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações Padrão
    |--------------------------------------------------------------------------
    |
    | Configurações de fallback caso uma oferta não seja encontrada.
    |
    */
    'default' => [
        'oferta_id' => 'oferta1',
        'checkout_id' => env('CHECKOUT_DEFAULT', env('CHECKOUT_ID')),
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs de Exemplo
    |--------------------------------------------------------------------------
    |
    | Exemplos de URLs que podem ser usadas para testar o sistema:
    |
    | globalpaymnts.com/checkout/?id=oferta1
    | globalpaymnts.com/checkout/?id=oferta2
    | globalpaymnts.com/checkout/?id=oferta3
    | globalpaymnts.com/checkout/?id=oferta4
    |
    */
];
