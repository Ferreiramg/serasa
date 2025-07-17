<?php

use Ferreiramg\TecnospeedSerasa\Facades\TecnospeedSerasa;
use Illuminate\Support\Facades\Facade;

it('extends laravel facade', function () {
    $reflection = new ReflectionClass(TecnospeedSerasa::class);
    expect($reflection->isSubclassOf(Facade::class))->toBeTrue();
});

it('returns correct facade accessor', function () {
    // Use reflection to access the protected method
    $reflection = new ReflectionClass(TecnospeedSerasa::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);

    $facade = new TecnospeedSerasa;
    $accessor = $method->invoke($facade);

    expect($accessor)->toBe('tecnospeed-serasa');
});

it('resolves service from container', function () {
    $service = TecnospeedSerasa::getFacadeRoot();
    expect($service)->toBeInstanceOf(\Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService::class);
});
