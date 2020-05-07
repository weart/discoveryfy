<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls\Tracks;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
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
 * Modify one track
 *
 * Module       PollsTracks
 * Class        PutController
 * OperationId  track.put (or track.modify)
 * Operation    PUT
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}
 * Security     Check poll->has who_can_add_track & membership
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Tracks::class;

    /** @var string */
    protected $resource    = Relationships::TRACK;

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

        $conditions =   [ 'poll_id = :poll_id:',                'id = :track_id:' ];
        $bind =         [ 'poll_id' => $poll_uuid,              'track_id' => $track_uuid ];
        $bindTypes =    [ 'poll_id' => Column::BIND_PARAM_STR,  'track_id' => Column::BIND_PARAM_STR ];

        if ($this->auth->getUser()) {
            if (!$this->auth->getUser()->isAdmin()) {
                $conditions[] = 'user_id = :user_id:';
                $bind['user_id'] = $this->auth->getUser()->get('id');
                $bindTypes['user_id'] = Column::BIND_PARAM_STR;
            }
        } else {
            $conditions[] = 'session_id = :session_id:';
            $bind['session_id'] = $this->auth->getSession()->get('id');
            $bindTypes['session_id'] = Column::BIND_PARAM_STR;
        }
        $this->track = Tracks::findFirst([
            'conditions' => implode(' AND ', $conditions),
            'bind'       => $bind,
            'bindTypes'  => $bindTypes,
        ]);
        if (!$this->track) {
            throw new UnauthorizedException('Only admins and owners can modify a track');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        // Update poll information
        $this->updateTrack($this->track);

        // Return the object
        return $this->sendApiData($this->track);
    }

    private function updateTrack(Tracks $track): Tracks
    {
        $attrs = [
            'artist'            => Filter::FILTER_STRING,
            'name'              => Filter::FILTER_STRING,
            'spotify_uri'       => Filter::FILTER_STRING,
            'youtube_uri'       => Filter::FILTER_STRING
        ];

        foreach ($attrs as $attr => $filter) {
            if ($this->request->hasPut($attr)) {
                $track->set($attr, $this->request->getPut($attr, $filter));
            }
        }

        if (true !== $track->validation() || true !== $track->save()) {
            if (false === $track->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing track');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $track->getMessages());
        }

        return $track;
    }
}
