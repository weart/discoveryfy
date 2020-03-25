<?php

namespace Discoveryfy\Tests\unit\config;

use Phalcon\Api\Providers\CliDispatcherProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\ModelsCacheProvider;
use Phalcon\Api\Providers\FiltersProvider;
use Phalcon\Api\Providers\SecurityProvider;
use Phalcon\Api\Providers\AuthProvider;
use UnitTester;
use function Phalcon\Api\Core\appPath;

class ProvidersCest
{
    public function checkApiProviders(UnitTester $I)
    {
        $providers = require(appPath('config/providers.api.php'));

        $I->assertEquals(ConfigProvider::class, $providers[0]);
        $I->assertEquals(LoggerProvider::class, $providers[1]);
        $I->assertEquals(ErrorHandlerProvider::class, $providers[2]);
        $I->assertEquals(DatabaseProvider::class, $providers[3]);
        $I->assertEquals(ModelsMetadataProvider::class, $providers[4]);
        $I->assertEquals(RequestProvider::class, $providers[5]);
        $I->assertEquals(ResponseProvider::class, $providers[6]);
        $I->assertEquals(RouterProvider::class, $providers[7]);
        $I->assertEquals(CacheDataProvider::class, $providers[8]);
        $I->assertEquals(ModelsCacheProvider::class, $providers[9]);
        $I->assertEquals(FiltersProvider::class, $providers[10]);
        $I->assertEquals(SecurityProvider::class, $providers[11]);
        $I->assertEquals(AuthProvider::class, $providers[12]);
    }

    public function checkCliProviders(UnitTester $I)
    {
        $providers = require(appPath('config/providers.cli.php'));

        $I->assertEquals(ConfigProvider::class, $providers[0]);
        $I->assertEquals(LoggerProvider::class, $providers[1]);
        $I->assertEquals(ErrorHandlerProvider::class, $providers[2]);
        $I->assertEquals(DatabaseProvider::class, $providers[3]);
        $I->assertEquals(ModelsMetadataProvider::class, $providers[4]);
        $I->assertEquals(CliDispatcherProvider::class, $providers[5]);
        $I->assertEquals(CacheDataProvider::class, $providers[6]);
    }
}
