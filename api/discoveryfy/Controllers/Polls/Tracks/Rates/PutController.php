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
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
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

        if ($this->auth->getUser()) {
//            if ($this->auth->getUser()->isAdmin()) {
//                return $parameters;
//            }
            $this->vote = Tracks::findFirst([
                'conditions' => 'poll_id = :poll_id: AND track_id = :track_id: AND user_id = :user_id:',
                'bind'       => [
                    'poll_id' => $poll_uuid,
                    'track_id' => $track_uuid,
                    'user_id' => $this->auth->getUser()->get('id')
                ],
                'bindTypes'  => [
                    'poll_id' => Column::BIND_PARAM_STR,
                    'track_id' => Column::BIND_PARAM_STR,
                    'user_id' => Column::BIND_PARAM_STR
                ],
            ]);
        } else {
            $this->vote = Tracks::findFirst([
                'conditions' => 'poll_id = :poll_id: AND track_id = :track_id: AND session_id = :session_id:',
                'bind'       => [
                    'poll_id' => $poll_uuid,
                    'track_id' => $track_uuid,
                    'session_id' => $this->auth->getSession()->get('id')
                ],
                'bindTypes'  => [
                    'poll_id' => Column::BIND_PARAM_STR,
                    'track_id' => Column::BIND_PARAM_STR,
                    'session_id' => Column::BIND_PARAM_STR
                ],
            ]);
        }
//        if (!$this->vote) {
//            throw new UnauthorizedException('Only the owner of a vote can modify it');
//        }
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
}
