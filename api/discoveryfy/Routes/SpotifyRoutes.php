<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Spotify\PostController as SpotifyPostController;
use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class SpotifyRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(SpotifyPostController::class, '/spotify', 'post')
        ];
    }
}


