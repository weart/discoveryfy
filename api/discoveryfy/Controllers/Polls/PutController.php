<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Discoveryfy\Workers\UpdatePollUpdateSpotifyPlaylistHistoricWorker;
use Discoveryfy\Workers\UpdatePollUpdateSpotifyPlaylistWinnerWorker;
use Discoveryfy\Workers\UpdatePollUpdateSpotifyPlaylistWorker;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
//use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Api\Providers\JobsProvider;
use Phalcon\Api\Queue\JobManager;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Resultset\Complex;
use Phalcon\Mvc\Model\Row;

/**
 * Modify one poll
 *
 * Module       Polls
 * Class        PutController
 * OperationId  poll.put (or poll.modify)
 * Operation    PUT
 * OperationUrl /polls/{poll_uuid}
 * Security     Only allowed to the owner or admins of the group
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{

    /** @var string */
    protected $model       = Polls::class;

    /** @var string */
    protected $resource    = Relationships::POLL;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    protected function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        $rtn = Polls::getUserMembership($parameters['id'], $this->auth->getUser()->get('id'));

        // Check if user is admin or owner of the group
        $poll = $this->checkUserMembership($rtn);

        // Update poll information
        $changedFields = $this->updatePoll($poll);

//        $this->eventsManager->fire('poll:modify', $this, $poll);
        if (array_intersect($changedFields, [ 'name', 'description', 'spotify_playlist_public', 'spotify_playlist_collaborative' ])) {
            $this->getJobManager()->addJob(UpdatePollUpdateSpotifyPlaylistWorker::class, $poll->toArray());
        }
        if (array_intersect($changedFields, [ 'name', 'description' ])) {
            if (!empty($poll->get('spotify_playlist_winner_uri'))) {
                $this->getJobManager()->addJob(UpdatePollUpdateSpotifyPlaylistWinnerWorker::class, $poll->toArray());
            }
            if (!empty($poll->get('spotify_playlist_historic_uri'))) {
                $this->getJobManager()->addJob(UpdatePollUpdateSpotifyPlaylistHistoricWorker::class, $poll->toArray());
            }
        }

        // Return the object
        return $this->sendApiData($poll);
    }

    private function checkUserMembership(Row $rtn): Polls
    {
        if (!in_array($rtn->member->get('rol'), ['ROLE_ADMIN', 'ROLE_OWNER'])) {
            throw new UnauthorizedException('Only admins and owners can modify a poll');
        }
        return $rtn->poll;
    }

    private function updatePoll(Polls $poll): array
    {
        $attrs = [
            'name'                              => Filter::FILTER_STRING,
            'description'                       => Filter::FILTER_STRING,
            'spotify_playlist_images'           => Filter::FILTER_STRING, //array, saved in json
            'spotify_playlist_public'           => Filter::FILTER_BOOL,
//            'spotify_playlist_collaborative'    => Filter::FILTER_BOOL, //Disable by now until sync Task is created
            'spotify_playlist_uri'              => Filter::FILTER_STRING,
            'spotify_playlist_winner_uri'       => Filter::FILTER_STRING,
            'spotify_playlist_historic_uri'     => Filter::FILTER_STRING,
            'start_date'                        => Filter::FILTER_STRING,
            'end_date'                          => Filter::FILTER_STRING,
            'restart_date'                      => Filter::FILTER_STRING,
            'public_visibility'                 => Filter::FILTER_BOOL,
            'public_votes'                      => Filter::FILTER_BOOL,
            'anon_can_vote'                     => Filter::FILTER_BOOL,
            'who_can_add_track'                 => Filter::FILTER_STRING,
            'anon_votes_max_rating'             => Filter::FILTER_ABSINT,
            'user_votes_max_rating'             => Filter::FILTER_ABSINT,
            'multiple_user_tracks'              => Filter::FILTER_BOOL,
            'multiple_anon_tracks'              => Filter::FILTER_BOOL

        ];

        foreach ($attrs as $attr => $filter) {
            if ($this->request->hasPut($attr)) {
                $poll->set($attr, $this->request->getPut($attr, $filter));
            }
        }

        $changedFields = $poll->getChangedFields();

        if (true !== $poll->validation() || true !== $poll->save()) {
            if (false === $poll->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing poll');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $poll->getMessages());
        }

        return $changedFields;
    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
