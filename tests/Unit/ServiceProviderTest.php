<?php

use Ferreiramg\TecnospeedSerasa\TecnospeedSerasaServiceProvider;
use Illuminate\Support\ServiceProvider;

it('extends laravel service provider', function () {
    $provider = new TecnospeedSerasaServiceProvider(app());
    expect($provider)->toBeInstanceOf(ServiceProvider::class);
});

it('registers service bindings', function () {
    expect(app()->bound('tecnospeed-serasa'))->toBeTrue();
});

it('publishes configuration file', function () {
    $provider = new TecnospeedSerasaServiceProvider(app());
    $provider->boot();

    // Check if configuration is published
    expect(config('tecnospeed-serasa'))->not->toBeNull();
});
