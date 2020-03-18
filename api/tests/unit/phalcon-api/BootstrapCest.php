<?php

namespace Discoveryfy\Tests\unit\Phalcon\Api;

use Phalcon\Api\Bootstrap\Api;
use Phalcon\Api\Http\Response;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use UnitTester;

class BootstrapCest
{
    public function checkBootstrap(UnitTester $I)
    {
        $bootstrap = new Api();
        $bootstrap->setup();

        $I->assertTrue($bootstrap->getContainer() instanceof FactoryDefault);
        $I->assertTrue($bootstrap->getResponse() instanceof Response);
        $I->assertTrue($bootstrap->getApplication() instanceof Micro);
    }
}
