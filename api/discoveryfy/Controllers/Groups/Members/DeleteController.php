<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups\Members;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Users;
//use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Db\Column;
use Phalcon\Http\ResponseInterface;

/**
 * Delete one membership
 *
 * Module       GroupsMembers
 * Class        DeleteController
 * OperationId  member.delete
 * Operation    DELETE
 * OperationUrl /groups/{group_uuid}/members/{user_uuid}
 * Security     Only allowed to the owner and admins of the group
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class DeleteController extends BaseItemApiController
{
    // Method 1: Using BaseItemApiController functions
    protected function checkSecurity(array $parameters = []): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }
        return $parameters;
    }

    protected function coreAction(array $parameters = []): ResponseInterface
    {
        $group_uuid = $parameters['id'];
        $user_uuid = $parameters['sub.id'];

        // Check if user can delete this membership of the group
        $org = $this->checkUserMembership($group_uuid, $user_uuid);

        // Delete the membership
        $this->deleteMembership($org, $user_uuid);

        return $this->response->sendNoContent();
    }

    /* Method 2: All in here
    public function callAction(string $group_uuid = '', string $user_uuid = ''): ResponseInterface
    {
        $group_uuid = $this->checkId($group_uuid);
        $user_uuid = $this->checkId($user_uuid);

        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }

        // Check if user can delete this membership of the group
        $org = $this->checkUserMembership($group_uuid, $user_uuid);

        // Delete the membership
        $this->deleteMembership($org, $user_uuid);

        return $this->response->sendNoContent();
    }
    */

    private function checkUserMembership(string $group_uuid, string $user_uuid): Organizations
    {
        $requester = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));

        // ROLE_OWNER can delete anyone
        if ($requester->member->rol === 'ROLE_OWNER') {
            return $requester->org;

        // ROLE_ADMIN can delete 'ROLE_INVITED', 'ROLE_MEMBER', 'ROLE_ADMIN' (anyone beside ROLE_OWNER)
        } elseif ($requester->member->rol === 'ROLE_ADMIN') {
            $target = Organizations::getUserMembership($group_uuid, $user_uuid);
            if ($target->member->rol === 'ROLE_OWNER') {
                throw new UnauthorizedException('Owner cannot be deleted');
            }
            return $requester->org;
        }
        throw new UnauthorizedException('Without enough permissions to delete this user');
    }

    private function deleteMembership(Organizations $org, string $user_uuid)
    {
        // Check if the membership exist
        $membership = Memberships::findFirst([
            'conditions' => 'user_id = :user_id: AND organization_id = :organization_id:',
            'bind'       => [
                'user_id' => $user_uuid,
                'organization_id' => $org->get('id'),
            ],
            'bindTypes'  => [
                'user_id' => Column::BIND_PARAM_STR,
                'organization_id' => Column::BIND_PARAM_STR,
            ],
        ]);
        if (is_null($membership) || true !== $membership->delete()) {
            throw new InternalServerErrorException('Error deleting the membership');
        }
//        return $membership;
    }
}
