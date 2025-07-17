<?php

use Ferreiramg\TecnospeedSerasa\DTOs\ConsultationResponse;

it('creates consultation response successfully', function () {
    $data = [
        'status' => 'processando',
        'protocolo' => 'PROT123456',
        'data' => ['some' => 'data'],
    ];

    $response = new ConsultationResponse($data);

    expect($response->getStatus())->toBe('processando');
    expect($response->getProtocolo())->toBe('PROT123456');
    expect($response->isSuccess())->toBeTrue();
});

it('handles consultation response errors', function () {
    $data = [
        'code' => 400,
        'message' => 'Something went wrong',
    ];

    $response = new ConsultationResponse($data);

    expect($response->getCode())->toBe(400);
    expect($response->isSuccess())->toBeFalse();
    expect($response->hasError())->toBeTrue();
    expect($response->getMessage())->toBe('Something went wrong');
});

it('detects completed consultation response', function () {
    $data = [
        'status' => 'concluido',
        'protocolo' => 'PROT123456',
        'resultado' => 'consultation result',
    ];

    $response = new ConsultationResponse($data);

    expect($response->isCompleted())->toBeTrue();
    expect($response->isSuccess())->toBeTrue();
    expect($response->getResultado())->toBe('consultation result');
});

it('handles response with errors array', function () {
    $data = [
        'errors' => [
            [
                'message' => 'Error from API',
                'internalCode' => 1001,
            ],
        ],
    ];

    $response = new ConsultationResponse($data);

    expect($response->getErrors())->toHaveCount(1);
    expect($response->getErrorMessage())->toBe('Error from API');
    expect($response->getInternalCode())->toBe(1001);
});

it('handles processing status', function () {
    $data = ['status' => 'processando'];
    $response = new ConsultationResponse($data);

    expect($response->isProcessing())->toBeTrue();
    expect($response->isCompleted())->toBeFalse();
});

it('detects unauthorized response', function () {
    $data = ['code' => 401];
    $response = new ConsultationResponse($data);

    expect($response->isUnauthorized())->toBeTrue();
});

it('detects unprocessable entity response', function () {
    $data = ['code' => 422];
    $response = new ConsultationResponse($data);

    expect($response->isUnprocessableEntity())->toBeTrue();
});

it('can convert to array and json', function () {
    $data = [
        'status' => 'concluido',
        'protocolo' => 'PROT123456',
    ];

    $response = new ConsultationResponse($data);

    expect($response->toArray())->toBe($data);
    expect($response->toJson())->toBeString();
    expect(json_decode($response->toJson(), true))->toBe($data);
});

it('can get document and consultation code', function () {
    $data = [
        'documento' => '12345678901',
        'codConsulta' => '602',
    ];

    $response = new ConsultationResponse($data);

    expect($response->getDocumento())->toBe('12345678901');
    expect($response->getCodConsulta())->toBe('602');
});

it('can get html result', function () {
    $data = ['html' => '<html>Result</html>'];
    $response = new ConsultationResponse($data);

    expect($response->getHtml())->toBe('<html>Result</html>');
});

it('handles finalizado status', function () {
    $data = ['status' => 'finalizado'];
    $response = new ConsultationResponse($data);

    expect($response->isCompleted())->toBeTrue();
    expect($response->isSuccess())->toBeTrue();
});

it('can get all response data', function () {
    $data = [
        'status' => 'concluido',
        'protocolo' => 'PROT123456',
        'resultado' => 'consultation result',
        'documento' => '12345678901',
        'codConsulta' => '602',
        'html' => '<html>Result</html>',
        'custom_field' => 'custom_value',
    ];

    $response = new ConsultationResponse($data);

    expect($response->getData())->toBe($data);
    expect($response->getStatus())->toBe('concluido');
    expect($response->getProtocolo())->toBe('PROT123456');
    expect($response->getDocumento())->toBe('12345678901');
});

it('handles empty response data', function () {
    $response = new ConsultationResponse([]);

    expect($response->getData())->toBe([]);
    expect($response->getStatus())->toBeNull();
    expect($response->getProtocolo())->toBeNull();
    expect($response->isSuccess())->toBeFalse();
    expect($response->isCompleted())->toBeFalse();
    expect($response->isProcessing())->toBeFalse();
});

it('handles mixed error states', function () {
    $data = [
        'code' => 400,
        'message' => 'Bad Request',
        'errors' => [
            ['message' => 'Field validation failed'],
        ],
    ];

    $response = new ConsultationResponse($data);

    expect($response->hasError())->toBeTrue();
    expect($response->isSuccess())->toBeFalse();
    expect($response->getCode())->toBe(400);
    expect($response->getMessage())->toBe('Bad Request');
    expect($response->getErrors())->toHaveCount(1);
});
