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
//use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseCollectionApiController;
//use Phalcon\Api\Controllers\BaseItemApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
//use Phalcon\Api\Traits\FractalTrait;
//use Phalcon\Api\Transformers\BaseTransformer;
//use Phalcon\Http\ResponseInterface;
//use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Retrieves a Group Member
 *
 * Module       GroupsMembers
 * Class        GetCollectionController
 * OperationId  members.list
 * Operation    GET
 * OperationUrl /groups/{group_uuid}/members
 * Security     Only allowed to the owner and admins of the group
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetCollectionController extends BaseCollectionApiController
{
//    use FractalTrait;

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

    public function checkSecurity($parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }

        $group_uuid = $parameters['id'];

        // User requester is part of the group, or the group has public membership
        // Organization::public_membership == true || $requester->rol !== INVITED
        // @ToDo: Add Query Cache - @see $this->getResultsCache
        $res = Organizations::isPublicMembershipOrMember($group_uuid, $this->auth->getUser()->get('id'));
        if ($res->count() != 1) {
            throw new UnauthorizedException('Only available when the group has public_membership or you belong to the group');
        }

        $parameters = [
            'organization_id' => $group_uuid
        ];
        return $parameters;
    }
}
