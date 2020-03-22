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

        //By default the Content-Type is application-json
        $response = json_decode($actual, true);

        $I->assertEquals($response, [
            'errors' => [
                [
                    'code' => 404,
                    'status' => 404,
                    'title' => '404 (Not Found)'
                ]

            ]
        ]);
//        $I->assertEquals('1.0', $response['jsonapi']['version']);
//        $I->assertTrue(empty($response['data']));
//        $I->assertEquals(HttpCode::getDescription(404), $response['errors'][0]);
    }
}
