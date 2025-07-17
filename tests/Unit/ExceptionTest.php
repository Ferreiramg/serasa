<?php

use Ferreiramg\TecnospeedSerasa\Exceptions\TecnospeedSerasaException;

it('can create tecnospeed serasa exception', function () {
    $message = 'Test error message';
    $code = 500;

    $exception = new TecnospeedSerasaException($message, $code);

    expect($exception)->toBeInstanceOf(TecnospeedSerasaException::class);
    expect($exception->getMessage())->toBe($message);
    expect($exception->getCode())->toBe($code);
});

it('extends standard exception', function () {
    $exception = new TecnospeedSerasaException('Test error');

    expect($exception)->toBeInstanceOf(\Exception::class);
});

it('can create exception with previous exception', function () {
    $previousException = new Exception('Previous error');
    $message = 'Test error message';
    $code = 500;

    $exception = new TecnospeedSerasaException($message, $code, $previousException);

    expect($exception->getPrevious())->toBe($previousException);
});

it('can throw and catch tecnospeed serasa exception', function () {
    expect(fn () => throw new TecnospeedSerasaException('Test error'))
        ->toThrow(TecnospeedSerasaException::class, 'Test error');
});
