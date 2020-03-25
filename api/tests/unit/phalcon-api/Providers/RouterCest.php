<?php

namespace Discoveryfy\Tests\unit\Phalcon\Api\Providers;

use Phalcon\Api\Logger;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\RouterInterface;
use UnitTester;

class RouterCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $application = new Micro($diContainer);
        $diContainer->setShared('application', $application);
        $provider = new ConfigProvider();
        $provider->register($diContainer);
        $provider = new RouterProvider();
        $provider->register($diContainer);

        /** @var RouterInterface $router */
        $router   = $application->getRouter();
        $routes   = $router->getRoutes();
        $expected = [
            ['GET', '/login'],
            ['POST', '/login'],
            ['GET', '/register'],
            ['POST', '/register'],
            ['GET', '/sessions/{session_uuid}'],
            ['PUT', '/sessions/{session_uuid}'],
            ['GET', '/users/{user_uuid}'],
            ['PUT', '/users/{user_uuid}'],
            ['GET', '/test'],
//            ['GET', '/users/{recordId:[0-9]+}'],
//            ['GET', '/product-types/{recordId:[0-9]+}/relationships/{relationships:[a-zA-Z-,.]+}'],
        ];

        $I->assertEquals(count($expected), count($routes));
        foreach ($routes as $index => $route) {
            $I->assertEquals($expected[$index][0], $route->getHttpMethods());
            $I->assertEquals($expected[$index][1], $route->getPattern());
        }
    }
}
