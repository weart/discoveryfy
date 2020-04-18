<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups\Polls;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Phalcon\Api\Controllers\BaseCollectionApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;

/**
 * Create one new poll
 *
 * Module       GroupsPolls
 * Class        GetCollectionController
 * OperationId  poll.list
 * Operation    GET
 * OperationUrl /groups/{group_uuid}/polls
 * Security     Logged user is part of the group, or the group has public_visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetCollectionController extends BaseCollectionApiController
{
    /** @var string */
    protected $resource    = Relationships::POLL;

    public function checkSecurity($parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }

        $group_uuid = $parameters['id'];

        // User requester is part of the group, or the group has public membership
        // Organization::public_visibility == true || $requester->rol !== INVITED
        // @ToDo: Add Query Cache - @see $this->getResultsCache
        $res = Organizations::isPublicVisibilityOrMember($group_uuid, $this->auth->getUser()->get('id'));
        if ($res->count() != 1) {
            throw new UnauthorizedException('Only available when the group has public_membership or you belong to the group');
        }

        $parameters = [
            'organization_id' => $group_uuid
        ];
        return $parameters;
    }
}
