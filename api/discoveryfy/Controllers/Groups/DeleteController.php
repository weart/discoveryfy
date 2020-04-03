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
use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Http\ResponseInterface;

/**
 * Delete one group
 *
 * Module       Groups
 * Class        DeleteController
 * OperationId  group.delete
 * Operation    DELETE
 * OperationUrl /groups/{group_uuid}
 * Security     Only allowed to the owner (or admins?) of the group
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class DeleteController extends BaseItemApiController
{
    protected function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        // Check if user is admin or owner of the group
        $org = $this->checkUserMembership($parameters['id']);

        // SoftDelete the organization
        $rtn = $org->delete();
        if (true !== $rtn) {
            throw new InternalServerErrorException('Error deleting the group');
        }

        return $this->response->sendNoContent();
    }

    private function checkUserMembership($group_uuid): Organizations
    {
        $rtn = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));

//        if (!in_array($rtn->member->rol, ['ROLE_ADMIN', 'ROLE_OWNER'])) {
        if (!in_array($rtn->member->rol, ['ROLE_OWNER'])) {
            throw new UnauthorizedException('Only admins and owners can delete a group');
        }
        return $rtn->org;
    }
}
