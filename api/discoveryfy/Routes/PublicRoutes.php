<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Login\GetController as LoginGetController;
use Discoveryfy\Controllers\Login\PostController as LoginPostController;
use Discoveryfy\Controllers\Register\GetController as RegisterGetController;
use Discoveryfy\Controllers\Register\PostController as RegisterPostController;
use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class PublicRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(LoginGetController::class, '/login', 'get'),
            new ApiRoute(LoginPostController::class, '/login', 'post'),
            new ApiRoute(RegisterGetController::class, '/register', 'get'),
            new ApiRoute(RegisterPostController::class, '/register', 'post')
        ];
    }
}


