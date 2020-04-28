<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups\Members;

use Discoveryfy\Constants\Relationships;
//use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseItemApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
//use Phalcon\Api\Transformers\BaseTransformer;
//use Phalcon\Http\ResponseInterface;

/**
 * Retrieves a Group Member
 *
 * Module       GroupsMembers
 * Class        ItemApiController
 * OperationId  member.get
 * Operation    GET
 * OperationUrl /groups/{group_uuid}/members/{user_uuid}
 * Security     Logged user is part of the group, or the group has public membership
 *
 * @property Auth         $auth
 * @property \Phalcon\Mvc\Model\Manager|\Phalcon\Mvc\Model\ManagerInterface $modelsManager
 * #property Request      $request
 * #property Response     $response
 */
class GetItemController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Memberships::class;

    /** @var string */
    protected $resource    = Relationships::MEMBERSHIP;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    /** @var array */
//    protected $includes = [];

    // Method 1: Most logic in BaseItemApiController
    // @ToDo: Check How this code can be merged with GetCollectionController
    public function checkSecurity($parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }

        $group_uuid = $parameters['id'];
        $user_uuid = $parameters['sub.id'];

        // User requester is part of the group, or the group has public membership
        // Organization::public_membership == true || $requester->rol !== INVITED
        // @ToDo: Add Query Cache - @see $this->getResultsCache
        $res = Organizations::isPublicMembershipOrMember($group_uuid, $this->auth->getUser()->get('id'));
        if ($res->count() != 1) {
            throw new UnauthorizedException('Only available when the group has public_membership or you belong to the group');
        }

        $parameters = [
            'organization_id'   => $group_uuid,
            'user_id'           => $user_uuid
        ];
        return $parameters;
    }

    /* Method 2: Logic in this controller
    public function callAction(string $group_uuid = ''): ResponseInterface
    {
        // If Organization::public_membership == true
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }

        // Any membership is valid
        $rtn = $this->checkUserMembership($group_uuid);

        // Return the object
//        return $this->sendApiData([
//            'type' => 'membership',
//            'id' => $rtn->member->rol
//        ]);
        return $this->response
            ->setStatusCode(
                $this->response::OK,
                $this->response->getHttpCodeDescription($this->response::OK)
            )
            ->sendApiContent($this->request->getContentType(), [
                'type' => 'membership',
                'id' => $rtn->member->rol
            ]);
    }
    private function checkUserMembership($group_uuid): Memberships
    {
        $rtn = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));
        // Any membership is valid
//        if (!in_array($rtn->member->rol, ['ROLE_ADMIN', 'ROLE_OWNER'])) {
//            throw new UnauthorizedException('Only admins and owners can delete a group');
//        }
        return $rtn->member;
    }
    */
}
