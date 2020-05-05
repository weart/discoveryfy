<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Routes;

use Discoveryfy\Controllers\Groups\DeleteController as GroupsDeleteController;
use Discoveryfy\Controllers\Groups\GetCollectionController as GroupsGetCollectionController;
use Discoveryfy\Controllers\Groups\GetItemController as GroupsGetItemController;
use Discoveryfy\Controllers\Groups\PostController as GroupsPostController;
use Discoveryfy\Controllers\Groups\PutController as GroupsPutController;
use Discoveryfy\Controllers\Groups\Members\DeleteController as MembersDeleteController;
use Discoveryfy\Controllers\Groups\Members\GetCollectionController as MembersGetCollectionController;
use Discoveryfy\Controllers\Groups\Members\GetItemController as MembersGetItemController;
use Discoveryfy\Controllers\Groups\Members\PutController as MembersPutController;
use Discoveryfy\Controllers\Groups\Polls\GetCollectionController as GroupsPollsGetCollectionController;
use Discoveryfy\Controllers\Groups\Polls\PostController as PollsPostController;
use Phalcon\Api\Routes\ApiRoute;
use Phalcon\Api\Routes\RoutesInterface;

class GroupsRoutes implements RoutesInterface
{
    public function getRoutes(): array
    {
        return [
            new ApiRoute(GroupsDeleteController::class, '/groups/{group_uuid}', 'delete'),  // group.delete
            new ApiRoute(GroupsGetCollectionController::class, '/groups', 'get'),           // groups.list
            new ApiRoute(GroupsGetItemController::class, '/groups/{group_uuid}', 'get'),    // group.get
            new ApiRoute(GroupsPostController::class, '/groups', 'post'),                   // group.create
            new ApiRoute(GroupsPutController::class, '/groups/{group_uuid}', 'put'),        // group.put (or group.modify)

            new ApiRoute(MembersDeleteController::class, '/groups/{group_uuid}/members/{user_uuid}', 'delete'), // member.delete
            new ApiRoute(MembersGetCollectionController::class, '/groups/{group_uuid}/members', 'get'),         // members.list
            new ApiRoute(MembersGetItemController::class, '/groups/{group_uuid}/members/{user_uuid}', 'get'),   // member.get
            new ApiRoute(MembersPutController::class, '/groups/{group_uuid}/members/{user_uuid}', 'put'),       // member.put

            new ApiRoute(GroupsPollsGetCollectionController::class, '/groups/{group_uuid}/polls', 'get'),   // group.polls.list
            new ApiRoute(PollsPostController::class, '/groups/{group_uuid}/polls', 'post'),                 // poll.create
        ];
    }
}
