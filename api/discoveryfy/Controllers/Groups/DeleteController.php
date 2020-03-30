<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Users;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;
//use Phalcon\Mvc\Model\Query\Builder;

/**
 * Delete one group
 *
 * Module       Groups
 * Class        DeleteController
 * OperationId  group.delete
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class DeleteController extends Controller
{

    public function callAction(string $group_uuid = ''): ResponseInterface
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }

        // Check if user is admin or owner of the group
        $org = $this->checkUserMembership($group_uuid);

        // SoftDelete the organization
        $rtn = $org->delete();
        if (true !== $rtn) {
            throw new InternalServerErrorException('Error deleting the group');
        }

        /**
         * A successful response SHOULD be 200 (OK) if the response includes an entity describing the status,
         * 202 (Accepted) if the action has not yet been enacted,
         * or 204 (No Content) if the action has been enacted but the response does not include an entity.
         */
        return $this->response->setStatusCode($this->response::NO_CONTENT)->send();
    }

    private function checkUserMembership($group_uuid): Organizations
    {
        $rtn = $this->modelsManager->createBuilder()
            ->columns('org.*, member.rol as member_rol, user.id as user_id')
            ->from([ 'org' => Organizations::class])
            ->innerJoin(Memberships::class, 'org.id = member.organization_id', 'member')
            ->innerJoin(Users::class, 'member.user_id = user.id', 'user')
            ->where('org.id = :org_uuid: AND user.id = :user_uuid:')
            ->setBindTypes([ 'org_uuid' => \PDO::PARAM_STR, 'user_uuid' => \PDO::PARAM_STR ])
            ->setBindParams([ 'org_uuid' => $group_uuid, 'user_uuid' => $this->auth->getUser()->get('id') ])
            ->getQuery()->execute();

        if ($rtn->count() === 0) {
            throw new UnauthorizedException('Cannot modify this group');
        }
        if ($rtn->count() > 1) {
            throw new InternalServerErrorException('Only one membership should be possible');
        }
        $rtn = $rtn->getFirst();
        if ($rtn->org->get('id') !== $group_uuid || $rtn->user_id !== $this->auth->getUser()->get('id')) {
            throw new InternalServerErrorException('Strange error in the query');
        }
        if (!in_array($rtn->member_rol, ['ROLE_ADMIN', 'ROLE_OWNER'])) {
            throw new UnauthorizedException('Only admins and owners can delete a group');
        }
        return $rtn->org;
    }
}
