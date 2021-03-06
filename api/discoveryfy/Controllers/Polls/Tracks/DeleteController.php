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
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Discoveryfy\Workers\DeleteTrackDeleteTrackSpotifyPlaylist;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Providers\JobsProvider;
use Phalcon\Api\Queue\JobManager;
use Phalcon\Db\Column;
use Phalcon\Http\ResponseInterface;

/**
 * Delete one track
 *
 * Module       PollsTracks
 * Class        DeleteController
 * OperationId  track.delete
 * Operation    DELETE
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}
 * Security     Only allowed to the owner of the track or admins
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class DeleteController extends BaseItemApiController
{
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
            throw new UnauthorizedException('Only admins and owners can delete a track');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        /** @var Polls $poll */
        if (false === ($poll = Polls::findFirst([
            'conditions' => 'id = :id:',
            'bind' => [ 'id' => $parameters['id'] ],
            'bindTypes' => [ 'id' => Column::BIND_PARAM_STR ],
        ]))) {
            throw new InternalServerErrorException('Invalid poll uuid');
        }
        if (null === $poll) {
            throw new InternalServerErrorException('Invalid poll uuid null');
        }
//        $poll = $this->track->getRelated('poll');
        $spotify_playlist_uri = $poll->get('spotify_playlist_uri');

        // @ToDo: Race problem here... the playlist hasnt been created already
        if (empty($spotify_playlist_uri)) {
            return $this->response->sendNoContent();
        }
        $spotify_uri = $this->track->get('spotify_uri');

        // Delete the track
        $rtn = $this->track->delete();
        if (true !== $rtn) {
            throw new InternalServerErrorException('Error deleting the track');
        }

//        $this->eventsManager->fire('track:delete', $this, $this->track);
        $this->getJobManager()->addJob(DeleteTrackDeleteTrackSpotifyPlaylist::class, [
//            'poll_id' => $poll->get('id'),
//            'track_id' => $this->track->get('id'),
            'spotify_playlist_uri' => $spotify_playlist_uri,
            'spotify_uri' => $spotify_uri
        ]);

        return $this->response->sendNoContent();
    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
