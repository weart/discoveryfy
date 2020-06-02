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
use Discoveryfy\Workers\DeletePollDeleteSpotifyPlaylistHistoricWorker;
use Discoveryfy\Workers\DeletePollDeleteSpotifyPlaylistWinnerWorker;
use Discoveryfy\Workers\DeletePollDeleteSpotifyPlaylistWorker;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Providers\JobsProvider;
use Phalcon\Api\Queue\JobManager;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\Row;

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
            throw new UnauthorizedException('Only available to registered users');
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

//        $this->eventsManager->fire('poll:delete', $this, $poll);
        $this->getJobManager()->addJob(DeletePollDeleteSpotifyPlaylistWorker::class, $poll->toArray());
        if (!empty($poll->get('spotify_playlist_winner_uri'))) {
            $this->getJobManager()->addJob(DeletePollDeleteSpotifyPlaylistWinnerWorker::class, $poll->toArray());
        }
        if (!empty($poll->get('spotify_playlist_historic_uri'))) {
            $this->getJobManager()->addJob(DeletePollDeleteSpotifyPlaylistHistoricWorker::class, $poll->toArray());
        }

        return $this->response->sendNoContent();
    }

    private function checkUserMembership(Row $rtn): Polls
    {
        if (!in_array($rtn->member->get('rol'), ['ROLE_OWNER'])) {
            throw new UnauthorizedException('Only owners can delete a poll');
        }
        return $rtn->poll;
    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
