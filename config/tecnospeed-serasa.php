<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tecnospeed Serasa API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Tecnospeed Serasa
    | API integration. You can configure your credentials and API settings here.
    |
    */

    'base_url' => [
        'homologacao' => env('TECNOSPEED_SERASA_BASE_URL_HML', 'https://api.consultanegativacao.com.br/v2/homologacao'),
        'producao' => env('TECNOSPEED_SERASA_BASE_URL_PROD', 'https://api.consultanegativacao.com.br/v2'),
    ],

    'environment' => env('TECNOSPEED_SERASA_ENVIRONMENT', 'homologacao'), // homologacao ou producao

    'credentials' => [
        'cnpjsh' => env('TECNOSPEED_SERASA_CNPJ_SH'),
        'tokensh' => env('TECNOSPEED_SERASA_TOKEN_SH'),
        'cnpjUsuario' => env('TECNOSPEED_SERASA_CNPJ_USUARIO'),
        'login' => env('TECNOSPEED_SERASA_LOGIN'),
        'password' => env('TECNOSPEED_SERASA_PASSWORD'),
    ],

    'timeout' => env('TECNOSPEED_SERASA_TIMEOUT', 30),

    'retries' => env('TECNOSPEED_SERASA_RETRIES', 3),

    'cache' => [
        'enabled' => env('TECNOSPEED_SERASA_CACHE_ENABLED', true),
        'ttl' => env('TECNOSPEED_SERASA_CACHE_TTL', 300), // 5 minutes
    ],

    'endpoints' => [
        'consulta_assincrona' => '/consultas/assincrona',
        'consulta_protocolo' => '/consultas',
    ],

    'tipos_consulta' => [
        1 => 'Crednet PF ou PJ TOP',
        600 => 'Relatório Básico PF',
        601 => 'Relatório Básico PJ',
        602 => 'Relatório Avançado PF',
        603 => 'Relatório Avançado PJ',
    ],

    'tipos_retorno' => [
        'HTML' => 'Retorno em HTML pronto',
        'JSON' => 'Retorno em JSON estruturado',
    ],
];
