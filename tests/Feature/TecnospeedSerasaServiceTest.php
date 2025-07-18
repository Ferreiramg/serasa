<?php

use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest;
use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

it('can request consultation successfully', function () {
    // Mock HTTP response
    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'status' => 'processando',
            'protocolo' => 'PROT123456',
        ])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // Use reflection to inject the mock client
    $reflection = new ReflectionClass($service);
    $clientProperty = $reflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($service, $client);

    $request = new ConsultationRequest(
        documento: '11144477735', // CPF válido
        codConsulta: 602,
        uf: 'SP',
        retorno: 'json'
    );

    $response = $service->solicitarConsulta($request);

    expect($response)->not->toBeNull();
    expect($response->isSuccess())->toBeTrue();
    expect($response->getProtocolo())->toBe('PROT123456');
});

it('can query protocol status', function () {
    // Mock HTTP response
    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'status' => 'concluido',
            'resultado' => 'consultation result',
        ])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // Use reflection to inject the mock client
    $reflection = new ReflectionClass($service);
    $clientProperty = $reflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($service, $client);

    $response = $service->consultarProtocolo('PROT123456');

    expect($response)->not->toBeNull();
    expect($response->isCompleted())->toBeTrue();
});

it('can perform complete document consultation', function () {
    // Mock HTTP responses - simulate immediate completion
    $mock = new MockHandler([
        // First call - solicitar consulta returns already completed
        new Response(200, [], json_encode([
            'status' => 'concluido',
            'protocolo' => 'PROT123456',
            'resultado' => 'consultation result',
        ])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // Use reflection to inject the mock client
    $reflection = new ReflectionClass($service);
    $clientProperty = $reflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($service, $client);

    $response = $service->consultarPorDocumento('11144477735', 602, 'SP'); // CPF válido

    expect($response)->not->toBeNull();
    expect($response->isCompleted())->toBeTrue();
    expect($response->getResultado())->toBe('consultation result');
});
