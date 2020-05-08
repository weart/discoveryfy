<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls\Tracks\Rates;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Discoveryfy\Models\Votes;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
//use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Db\Column;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Resultset\Complex;
use Phalcon\Security\Random;

/**
 * Modify one rate
 *
 * Module       PollsTracksRates
 * Class        PutController
 * OperationId  rate.put (or rate.modify)
 * Operation    PUT
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}/rates
 * Security     Check poll->has anon_can_vote & membership, anon_votes_max_rating & user_votes_max_rating
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Votes::class;

    /** @var string */
    protected $resource    = Relationships::RATE;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    /** @var Votes */
    protected $vote;

    protected function checkSecurity(array $parameters): array
    {
        $poll_uuid = $parameters['id'];
        $track_uuid = $parameters['sub.id'];

        $poll = $this->getPoll($poll_uuid);
        if (!$poll) {
            throw new BadRequestException();
        }
        if (true !== $poll->get('public_votes')) {
            if (!$this->auth->getUser()) {
                throw new UnauthorizedException('Only available to registered users');
            }
            $membership = $this->getMembership($poll->get('organization_id'), $this->auth->getUser()->get('id'));
            if (!$membership) {
                throw new UnauthorizedException('Only available to group members');
            }
        }

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
//        if (!$this->vote) {
//            throw new UnauthorizedException('Only the owner of a vote can modify it');
//        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        //@ToDo: Check poll->has anon_can_vote & membership
        //@ToDo: Check anon_votes_max_rating & user_votes_max_rating
        if (!$this->vote) {
            $this->createVote($parameters);

        } else {
            // Update vote rate
            $this->vote->set('rate', $this->request->getPut('rate', Filter::FILTER_ABSINT));
        }

        if (true !== $this->vote->validation() || true !== $this->vote->save()) {
            if (false === $this->vote->validationHasFailed()) {
                throw new InternalServerErrorException('Error saving the vote');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $this->vote->getMessages());
        }

        // Return the object
        return $this->sendApiData($this->vote);
    }

    private function createVote(array $parameters)
    {
        $poll_uuid = $parameters['id'];
        $track_uuid = $parameters['sub.id'];

        $this->vote = new Votes();
        $this->vote
            ->set('id', (new Random())->uuid())
            ->set('poll_id', $poll_uuid)
            ->set('track_id', $track_uuid)
            ->set('session_id', $this->auth->getSession()->get('id'))
            ->set('rate', $this->request->getPut('rate', Filter::FILTER_ABSINT));

        if ($this->auth->getUser()) {
            $this->vote->set('user_id', $this->auth->getUser()->get('id'));
        }
    }

    /**
     * @param string $poll_uuid
     * @return mixed
     */
    private function getPoll(string $poll_uuid)
    {
        return Polls::findFirst([
            'conditions' => 'id = :poll_uuid:',
            'bind'       => [ 'poll_uuid' => $poll_uuid ],
            'bindTypes'  => [ 'poll_uuid' => Column::BIND_PARAM_STR ],
        ]);
    }

    /**
     * @ToDo Test if organization is not deleted
     * @param string $org_uuid
     * @param string $user_uuid
     * @return mixed
     */
    private function getMembership(string $org_uuid, string $user_uuid)
    {
        return Memberships::findFirst([
            'conditions' => 'user_id = :user_id: AND organization_id = :organization_id: AND rol != :rol:',
            'bind'       => [ 'user_id' => $user_uuid, 'organization_id' => $org_uuid, 'rol' => 'ROLE_INVITED' ],
            'bindTypes'  => [ 'user_id' => Column::BIND_PARAM_STR, 'organization_id' => Column::BIND_PARAM_STR, 'rol' => Column::BIND_PARAM_STR ],
        ]);
    }
}
