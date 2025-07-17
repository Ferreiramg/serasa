<?php

use Ferreiramg\TecnospeedSerasa\Facades\TecnospeedSerasa;
use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;

it('can access service through facade accessor', function () {
    // Use reflection to access the protected method
    $reflection = new ReflectionClass(TecnospeedSerasa::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    $facade = new TecnospeedSerasa;
    $accessor = $method->invoke($facade);

    expect($accessor)->toBe('tecnospeed-serasa');
});

it('validates facade service binding', function () {
    $service = app('tecnospeed-serasa');
    expect($service)->toBeInstanceOf(TecnospeedSerasaService::class);
});

it('can configure service through provider', function () {
    $config = config('tecnospeed-serasa');

    expect($config)->toBeArray();
    expect($config)->toHaveKey('environment');
    expect($config)->toHaveKey('credentials');
    expect($config)->toHaveKey('timeout');
});

it('has correct environment configuration', function () {
    $environment = config('tecnospeed-serasa.environment');
    $credentials = config('tecnospeed-serasa.credentials');

    expect($environment)->toBe('homologacao');
    expect($credentials)->toHaveKey('cnpjsh');
    expect($credentials)->toHaveKey('tokensh');
    expect($credentials['cnpjsh'])->toBe('test_cnpj_sh');
    expect($credentials['tokensh'])->toBe('test_token_sh');
});
