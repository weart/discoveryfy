<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Sessions\GetController as SessionGetController;
use Discoveryfy\Controllers\Sessions\PutController as SessionPutController;
use Discoveryfy\Controllers\Users\GetController as UserGetController;
use Discoveryfy\Controllers\Users\PutController as UserPutController;
use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class UserRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(SessionGetController::class, '/sessions/{session_uuid}', 'get'),
            new ApiRoute(SessionPutController::class, '/sessions/{session_uuid}', 'put'),
            new ApiRoute(UserGetController::class, '/users/{user_uuid}', 'get'),
            new ApiRoute(UserPutController::class, '/users/{user_uuid}', 'put')
        ];
    }
}


