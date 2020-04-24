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

    /** @var Tracks */
    protected $track;

    protected function checkSecurity(array $parameters): array
    {
        $poll_uuid = $parameters['id'];
        $track_uuid = $parameters['sub.id'];

        if ($this->auth->getUser()) {
            if ($this->auth->getUser()->isAdmin()) {
                return $parameters;
            }
            $this->track = Tracks::findFirst([
                'conditions' => 'id = :track_id: AND user_id = :user_id:',
                'bind'       => [
                    'track_id' => $track_uuid,
                    'user_id' => $this->auth->getUser()->get('id')
                ],
                'bindTypes'  => [
                    'poll_id' => Column::BIND_PARAM_STR,
                    'user_id' => Column::BIND_PARAM_STR
                ],
            ]);
        } else {
            $this->track = Tracks::findFirst([
                'conditions' => 'id = :track_id: AND session_id = :session_id:',
                'bind'       => [
                    'track_id' => $track_uuid,
                    'session_id' => $this->auth->getSession()->get('id')
                ],
                'bindTypes'  => [
                    'poll_id' => Column::BIND_PARAM_STR,
                    'session_id' => Column::BIND_PARAM_STR
                ],
            ]);
        }
        if (!$this->track) {
            throw new UnauthorizedException('Only admins and owners can modify a track');
        }
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        // Update poll information
        $this->updateRate($this->track);

        // Return the object
        return $this->sendApiData($this->track);
    }

    private function updateRate(Tracks $track): Tracks
    {
        $attrs = [
            'rate'          => Filter::FILTER_ABSINT
        ];

        foreach ($attrs as $attr => $filter) {
            if ($this->request->hasPut($attr)) {
                $track->set($attr, $this->request->getPut($attr, $filter));
            }
        }
        //@ToDo: Check poll->has anon_can_vote & membership
        //@ToDo: Check anon_votes_max_rating & user_votes_max_rating
        if (true !== $track->validation() || true !== $track->save()) {
            if (false === $track->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing poll');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $track->getMessages());
        }

        return $track;
    }
}
