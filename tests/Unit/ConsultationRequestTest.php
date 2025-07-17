<?php

use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationRequest;

it('creates consultation request successfully', function () {
    $request = new ConsultationRequest(
        documento: '12345678901',
        codConsulta: 602,
        uf: 'SP',
        retorno: 'json'
    );

    expect($request->getDocumento())->toBe('12345678901');
    expect($request->getCodConsulta())->toBe(602);
    expect($request->getUf())->toBe('SP');
    expect($request->getRetorno())->toBe('json');
});

it('converts consultation request to array', function () {
    $request = new ConsultationRequest(
        documento: '12345678901',
        codConsulta: 602,
        uf: 'SP',
        retorno: 'json'
    );

    $expected = [
        'documento' => '12345678901',
        'codConsulta' => 602,
        'retorno' => 'json',
        'uf' => 'SP',
    ];

    expect($request->toArray())->toBe($expected);
});

it('generates correct headers for consultation request', function () {
    $request = new ConsultationRequest(
        documento: '12345678901',
        codConsulta: 602,
        uf: 'SP',
        retorno: 'json'
    );

    $credentials = [
        'cnpjsh' => 'test_cnpj_sh',
        'tokensh' => 'test_token_sh',
        'cnpjUsuario' => 'test_cnpj_usuario',
        'login' => 'test_login',
        'password' => 'test_password',
    ];

    $headers = $request->getHeaders($credentials);

    expect($headers)->toHaveKey('cnpjsh');
    expect($headers)->toHaveKey('tokensh');
    expect($headers['cnpjsh'])->toBe('test_cnpj_sh');
    expect($headers['tokensh'])->toBe('test_token_sh');
});
