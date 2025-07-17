<?php

namespace Ferreiramg\TecnospeedSerasa;

use Ferreiramg\TecnospeedSerasa\Services\TecnospeedSerasaService;
use Illuminate\Support\ServiceProvider;

class TecnospeedSerasaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tecnospeed-serasa.php',
            'tecnospeed-serasa'
        );

        $this->app->singleton(TecnospeedSerasaService::class, function ($app) {
            return new TecnospeedSerasaService(
                config('tecnospeed-serasa')
            );
        });

        $this->app->alias(TecnospeedSerasaService::class, 'tecnospeed-serasa');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tecnospeed-serasa.php' => config_path('tecnospeed-serasa.php'),
            ], 'tecnospeed-serasa-config');
        }
    }
}
