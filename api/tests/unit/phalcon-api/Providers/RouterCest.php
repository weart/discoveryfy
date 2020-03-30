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
            // Public Routes
            ['GET', '/login'],
            ['POST', '/login'],
            ['GET', '/register'],
            ['POST', '/register'],

            // User Routes
            ['GET', '/sessions/{session_uuid}'],
            ['PUT', '/sessions/{session_uuid}'],
            ['GET', '/users/{user_uuid}'],
            ['PUT', '/users/{user_uuid}'],

            // Groups Routes
            ['DELETE', '/groups/{group_uuid}'],
            ['GET', '/groups'],
            ['GET', '/groups/{group_uuid}'],
            ['POST', '/groups'],
            ['PUT', '/groups/{group_uuid}'],

            ['DELETE', '/groups/{group_uuid}/members/{user_uuid}'],
            ['GET', '/groups/{group_uuid}/members'],
            ['GET', '/groups/{group_uuid}/members/{user_uuid}'],
            ['PUT', '/groups/{group_uuid}/members/{user_uuid}'],

            ['PUT', '/groups/{group_uuid}/polls'],
            ['POST', '/groups/{group_uuid}/polls'],

            // Polls Routes
//            ['GET', '/polls'],
//            ['GET', '/polls/{poll_uuid}'],
//            ['PUT', '/polls/{poll_uuid}'],
//            ['DELETE', '/polls/{poll_uuid}'],
//
//            ['POST', '/polls/{poll_uuid}/tracks'],
//            ['PUT', '/polls/{poll_uuid}/tracks/{track_uuid}'],
//            ['DELETE', '/polls/{poll_uuid}/tracks/{track_uuid}'],
//
//            ['PUT', '/polls/{poll_uuid}/tracks/{track_uuid}/rate'],
//            ['DELETE', '/polls/{poll_uuid}/tracks/{track_uuid}/rate'],

            // Test Routes
            ['GET', '/test'],
        ];

        $I->assertEquals(count($expected), count($routes), 'Different number of routes');
        foreach ($routes as $index => $route) {
            $I->assertEquals($expected[$index][1], $route->getPattern(), 'Different pattern');
            $I->assertEquals($expected[$index][0], $route->getHttpMethods(), 'Different method');
        }
    }
}
