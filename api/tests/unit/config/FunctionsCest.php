<?php

namespace Discoveryfy\Tests\unit\config;

use Discoveryfy\Constants\Relationships;
use UnitTester;
use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\appUrl;
use function Phalcon\Api\Core\envValue;

class FunctionsCest
{
    public function checkApppath(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__)));
        $I->assertEquals($path, appPath());
    }

    public function checkApppathWithParameter(UnitTester $I)
    {
        $path = dirname(dirname(dirname(__DIR__))) . '/config/config.php';
        $I->assertEquals($path, appPath('config/config.php'));
    }

    public function checkEnvvalueAsFalse(UnitTester $I)
    {
        putenv('SOMEVAL=false');
        $I->assertFalse(envValue('SOMEVAL'));
    }

    public function checkEnvvalueAsTrue(UnitTester $I)
    {
        putenv('SOMEVAL=true');
        $I->assertTrue(envValue('SOMEVAL'));
    }

    public function checkEnvvalueWithValue(UnitTester $I)
    {
        putenv('SOMEVAL=someval');
        $I->assertEquals('someval', envValue('SOMEVAL'));
    }

    public function checkEnvurlWithUrl(UnitTester $I)
    {
        $I->assertEquals(
            'https://api.discoveryfy.fabri.cat/users/1',
            appUrl('users', 1)
        );
    }
}
