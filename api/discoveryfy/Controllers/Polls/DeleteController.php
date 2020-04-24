<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Polls;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Resultset\Complex;

/**
 * Delete one poll
 *
 * Module       Polls
 * Class        DeleteController
 * OperationId  poll.delete
 * Operation    DELETE
 * OperationUrl /polls/{poll_uuid}
 * Security     Only allowed to the owner of the group
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
        $rtn = Polls::getUserMembership($parameters['id'], $this->auth->getUser()->get('id'));

        // Check if user is owner of the group
        $poll = $this->checkUserMembership($rtn);

        // SoftDelete the poll
        $rtn = $poll->delete();
        if (true !== $rtn) {
            throw new InternalServerErrorException('Error deleting the poll');
        }

        return $this->response->sendNoContent();
    }

    private function checkUserMembership(Complex $rtn): Polls
    {
        if (!in_array($rtn->member->get('rol'), ['ROLE_OWNER'])) {
            throw new UnauthorizedException('Only owners can delete a poll');
        }
        return $rtn->poll;
    }
}
