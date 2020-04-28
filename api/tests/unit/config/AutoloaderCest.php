<?php

namespace Discoveryfy\Tests\unit\config;

use Phalcon\Api\Http\Response;
use UnitTester;
use function function_exists;
use function Phalcon\Api\Core\appPath;

class AutoloaderCest
{
    public function checkDotenvVariables(UnitTester $I)
    {
        require appPath('phalcon-api/Core/autoload.php');

        $I->assertNotEquals(false, getenv('APP_DEBUG'), 'APP_DEBUG is not false');
        $I->assertNotEquals(false, getenv('APP_ENV'), 'APP_ENV is not false');
        $I->assertNotEquals(false, getenv('APP_URL'), 'APP_URL is not false');
        $I->assertNotEquals(false, getenv('APP_NAME'), 'APP_NAME is not false');
        $I->assertNotEquals(false, getenv('APP_BASE_URI'), 'APP_BASE_URI is not false');
        $I->assertNotEquals(false, getenv('APP_SUPPORT_EMAIL'), 'APP_SUPPORT_EMAIL is not false');
        $I->assertNotEquals(false, getenv('APP_TIMEZONE'), 'APP_TIMEZONE is not false');
        $I->assertNotEquals(false, getenv('CACHE_PREFIX'), 'CACHE_PREFIX is not false');
        $I->assertNotEquals(false, getenv('CACHE_LIFETIME'), 'CACHE_LIFETIME is not false');
        $I->assertNotEquals(false, getenv('MYSQL_DATABASE'), 'MYSQL_DATABASE is not false');
//        $I->assertNotEquals(false, getenv('LOG_PATH'));
//        $I->assertNotEquals(false, getenv('LOG_FILENAME'));
//        $I->assertNotEquals(false, getenv('LOG_FORMAT'));
//        $I->assertNotEquals(false, getenv('LOG_FORMAT_DATE'));
//        $I->assertNotEquals(false, getenv('LOG_CHANNEL'));
        $I->assertNotEquals(false, getenv('APP_VERSION'), 'APP_VERSION is not false');

        $I->assertEquals('true', getenv('APP_DEBUG'));
        $I->assertEquals('development', getenv('APP_ENV'));
        $I->assertEquals('https://api.discoveryfy.fabri.cat', getenv('APP_URL'));
        $I->assertEquals('/', getenv('APP_BASE_URI'));
        $I->assertEquals('discoveryfy@fabri.cat', getenv('APP_SUPPORT_EMAIL'));
        $I->assertEquals('UTC', getenv('APP_TIMEZONE'));
        $I->assertEquals('api_cache_', getenv('CACHE_PREFIX'));
        $I->assertEquals(86400, getenv('CACHE_LIFETIME'));
//        $I->assertEquals('api.log', getenv('LOG_FILENAME'));
//        $I->assertEquals('storage/logs/', getenv('LOG_PATH'));
//        $I->assertEquals('[%datetime%] %channel%.%level_name%: %message%', getenv('LOG_FORMAT'));
//        $I->assertEquals('Y-m-d\TH:i:sP', getenv('LOG_FORMAT_DATE'));
//        $I->assertEquals('api', getenv('LOG_CHANNEL'));
        $I->assertEquals('20200315', getenv('APP_VERSION'));
    }

    public function checkAutoloader(UnitTester $I)
    {
        require appPath('phalcon-api/Core/autoload.php');

        $class = new Response();
        $I->assertTrue($class instanceof Response);
        $I->assertTrue(function_exists('Phalcon\Api\Core\envValue'));
    }
}
