<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Polls\DeleteController as PollsGetCollectionController;
use Discoveryfy\Controllers\Polls\GetCollectionController as PollsGetItemController;
use Discoveryfy\Controllers\Polls\GetItemController as PollsPutController;
use Discoveryfy\Controllers\Polls\PutController as PollsDeleteController;

use Discoveryfy\Controllers\Polls\Tracks\PostController as TrackPostController;
use Discoveryfy\Controllers\Polls\Tracks\PutController as TrackPutController;
use Discoveryfy\Controllers\Polls\Tracks\DeleteController as TrackDeleteController;

use Discoveryfy\Controllers\Polls\Tracks\Rates\PutController as RatePutController;
use Discoveryfy\Controllers\Polls\Tracks\Rates\DeleteController as RateDeleteController;

use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class PollsRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(PollsGetCollectionController::class, '/polls', 'get'),                                 // polls.list
            new ApiRoute(PollsGetItemController::class, '/polls/{poll_uuid}', 'get'),                           // poll.get
            new ApiRoute(PollsPutController::class, '/polls/{poll_uuid}', 'put'),                               // poll.put
            new ApiRoute(PollsDeleteController::class, '/polls/{poll_uuid}', 'delete'),                         // poll.delete

            new ApiRoute(TrackPostController::class, '/polls/{poll_uuid}/tracks', 'post'),                      // track.create
            new ApiRoute(TrackPutController::class, '/polls/{poll_uuid}/tracks/{track_uuid}', 'put'),           // track.put
            new ApiRoute(TrackDeleteController::class, '/polls/{poll_uuid}/tracks/{track_uuid}', 'delete'),     // track.delete

            new ApiRoute(RatePutController::class, '/polls/{poll_uuid}/tracks/{track_uuid}/rate', 'put'),       // rate.put
            new ApiRoute(RateDeleteController::class, '/polls/{poll_uuid}/tracks/{track_uuid}/rate', 'delete'), // rate.delete
        ];
    }
}
