<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls\Tracks;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Discoveryfy\Models\Users;
use Discoveryfy\Models\Votes;
use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Db\Column;
use Phalcon\Http\ResponseInterface;

/**
 * Delete one rate
 *
 * Module       PollsTracksRates
 * Class        DeleteController
 * OperationId  rate.delete
 * Operation    DELETE
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}/rates
 * Security     Only allowed to the owner of the rate
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class DeleteController extends BaseItemApiController
{
    /** @var Votes */
    protected $vote;

    protected function checkSecurity(array $parameters): array
    {
        $poll_uuid = $parameters['id'];
        $track_uuid = $parameters['sub.id'];

        $conditions =   [ 'poll_id = :poll_id:',                'track_id = :track_id:' ];
        $bind =         [ 'poll_id' => $poll_uuid,              'track_id' => $track_uuid ];
        $bindTypes =    [ 'poll_id' => Column::BIND_PARAM_STR,  'track_id' => Column::BIND_PARAM_STR ];

        if ($this->auth->getUser()) {
//            if (!$this->auth->getUser()->isAdmin()) {
                $conditions[] = 'user_id = :user_id:';
                $bind['user_id'] = $this->auth->getUser()->get('id');
                $bindTypes['user_id'] = Column::BIND_PARAM_STR;
//            }
        } else {
            $conditions[] = 'session_id = :session_id:';
            $bind['session_id'] = $this->auth->getSession()->get('id');
            $bindTypes['session_id'] = Column::BIND_PARAM_STR;
        }
        $this->vote = Votes::findFirst([
            'conditions' => implode(' AND ', $conditions),
            'bind'       => $bind,
            'bindTypes'  => $bindTypes,
        ]);

        if (!$this->vote) {
            throw new UnauthorizedException('Only the owner of a vote can deleted it');
        }
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        // SoftDelete the organization
        $rtn = $this->vote->delete();
        if (true !== $rtn) {
            throw new InternalServerErrorException('Error deleting the group');
        }

        return $this->response->sendNoContent();
    }
}
