<?php

namespace Ferreiramg\TecnospeedSerasa\Tests;

use Ferreiramg\TecnospeedSerasa\TecnospeedSerasaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TecnospeedSerasaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('tecnospeed-serasa.environment', 'homologacao');
        $app['config']->set('tecnospeed-serasa.credentials.cnpjsh', 'test_cnpj_sh');
        $app['config']->set('tecnospeed-serasa.credentials.tokensh', 'test_token_sh');
        $app['config']->set('tecnospeed-serasa.credentials.cnpjUsuario', 'test_cnpj_usuario');
        $app['config']->set('tecnospeed-serasa.credentials.login', 'test_login');
        $app['config']->set('tecnospeed-serasa.credentials.password', 'test_password');
        $app['config']->set('tecnospeed-serasa.timeout', 30);
        $app['config']->set('tecnospeed-serasa.retries', 3);
        $app['config']->set('tecnospeed-serasa.cache.enabled', false);
        $app['config']->set('tecnospeed-serasa.cache.ttl', 300);
    }
}
