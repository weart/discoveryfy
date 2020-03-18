<?php

namespace Discoveryfy\Tests\unit;

use CliTester;
use Codeception\Util\HttpCode;
use function Phalcon\Api\Core\appPath;

class BootstrapCest
{
    public function checkBootstrap(CliTester $I)
    {
        $_GET['_url'] = '/';
        ob_start();
        require appPath('public/index.php');
        $actual = ob_get_contents();
        ob_end_clean();

        $results = json_decode($actual, true);

        //Content-Type: application-json
//        $I->assertEquals('1.0', $results['jsonapi']['version']);
//        $I->assertTrue(empty($results['data']));
//        $I->assertEquals(HttpCode::getDescription(404), $results['errors'][0]);

        //Content-Type: undefined
        $I->assertEquals($results['code'], 400);
        $I->assertEquals($results['status'], 'error');
        $I->assertEquals($results['message'], 'Undefined content type');
    }
}
