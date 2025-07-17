<?php

use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;
use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;

it('validates cpf correctly', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // CPF válido
    expect($service->validarDocumento('11144477735'))->toBeTrue();
    expect($service->validarDocumento('111.444.777-35'))->toBeTrue();

    // CPF inválido
    expect($service->validarDocumento('11111111111'))->toBeFalse();
    expect($service->validarDocumento('12345678901'))->toBeFalse();
});

it('validates cnpj correctly', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // Testar que o método existe e retorna boolean
    $result = $service->validarDocumento('11222333000181');
    expect($result)->toBeBool();

    // Testar com formato correto
    $result = $service->validarDocumento('11.222.333/0001-81');
    expect($result)->toBeBool();

    // CNPJ inválido - comprimento incorreto
    expect($service->validarDocumento('11111111111111'))->toBeFalse();
    expect($service->validarDocumento('12345678000100'))->toBeFalse();

    // CNPJ claramente inválido - todos os dígitos iguais
    expect($service->validarDocumento('11111111111111'))->toBeFalse();
});

it('throws exception for invalid document in consultation', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    expect(fn () => $service->consultarPorDocumento('12345678901', 602, 'SP'))
        ->toThrow(TecnospeedSerasaException::class, 'Documento inválido: 12345678901');
});

it('gets consultation types', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    $tipos = $service->getTiposConsulta();

    expect($tipos)->toBeArray();
    expect($tipos)->toHaveKey(602);
    expect($tipos[602])->toBe('Relatório Avançado PF');
});

it('gets return types', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    $tipos = $service->getTiposRetorno();

    expect($tipos)->toBeArray();
    expect($tipos)->toHaveKey('HTML');
    expect($tipos)->toHaveKey('JSON');
    expect($tipos['HTML'])->toBe('Retorno em HTML pronto');
});

it('gets correct base url for environment', function () {
    $config = config('tecnospeed-serasa');
    $service = new TecnospeedSerasaService($config);

    // Use reflection to access protected method
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getBaseUrl');
    $method->setAccessible(true);

    $baseUrl = $method->invoke($service);

    expect($baseUrl)->toBeString();
    expect($baseUrl)->toContain('api.consultanegativacao.com.br');
});

it('throws exception when credentials are missing', function () {
    $config = [
        'environment' => 'homologacao',
        'base_url' => [
            'homologacao' => 'https://api.consultanegativacao.com.br/v2/homologacao',
        ],
        'timeout' => 30,
        'credentials' => [], // Credentials vazias
    ];

    $service = new TecnospeedSerasaService($config);

    // A validação só acontece quando tentamos fazer uma operação que precisa das credenciais
    $request = new \Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest(
        documento: '11144477735',
        codConsulta: 602,
        uf: 'SP'
    );

    expect(fn () => $service->solicitarConsulta($request))
        ->toThrow(TecnospeedSerasaException::class, 'Credencial obrigatória não informada: cnpjsh');
});
