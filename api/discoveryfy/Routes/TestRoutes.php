<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Tests\TestController;
use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class TestRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(TestController::class, '/test', 'get')
        ];
    }
    // Class, Method, Route, Handler
//        return [LoginController::class,        '/login',     'post', '/'];

    /* Maybe is better to use a more Phalcon native way?
    $routes = new Group(
        [
            'module'     => 'blog',
            'controller' => 'index',
        ]
    );

    // All the routes start with /blog
    $routes->setPrefix('/blog');

    // Add a route to the group
    $routes->add('/save', [
        'action' => 'save',
    ]);

    // Add another route to the group
    $routes->add('/edit/{id}', [
        'action' => 'edit',
    ]);

    // This route maps to a controller different than the default
    $routes->add('/blog', [
        'controller' => 'about',
        'action'     => 'index',
    ]);

    return $routes;
    */
}


