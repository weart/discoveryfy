<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
//use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
//use Phalcon\Api\Controllers\BaseController;
use Discoveryfy\Models\Polls;
//use Discoveryfy\Models\Users;
//use Phalcon\Mvc\Controller;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
//use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
use Phalcon\Security\Random;

/**
 * Create one new poll
 *
 * Module       GroupsPolls
 * Class        PostController
 * OperationId  poll.create
 * Operation    POST
 * OperationUrl /groups/{group_uuid}
 * Security     Logged user is part of the group, and the field who_can_create_polls applies for the member rol
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends BaseItemApiController
{
    use FractalTrait;

    /** @var string */
//    protected $model       = Polls::class;

    /** @var string */
    protected $resource    = Relationships::POLL;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    protected function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        $group_uuid = $parameters['id'];

        if (empty($this->request->getPost())) {
            throw new BadRequestException('Empty post');
        }
        // @ToDo: How this check can be moved into validation?
        if ($this->request->getPost('name') !== $this->request->getPost('name', Filter::FILTER_STRIPTAGS)) {
            throw new BadRequestException('Invalid name');
        }
        if ($this->request->getPost('description') !== $this->request->getPost('description', Filter::FILTER_STRIPTAGS)) {
            throw new BadRequestException('Invalid description');
        }

        $this->checkUserMembership($group_uuid);

        // @ToDo: Double filtering, here and in the setter, maybe one can be removed?
        $poll = new Polls();
        $poll
            ->set('id', (new Random())->uuid())
            ->set('organization_id', $group_uuid)
            ->set('name', $this->request->getPost('name', Filter::FILTER_STRIPTAGS))
            ->set('description', $this->request->getPost('description', Filter::FILTER_STRIPTAGS))
            ->set('start_date', $this->request->getPost('start_date', Filter::FILTER_STRING, ''))
            ->set('end_date', $this->request->getPost('end_date', Filter::FILTER_STRING, ''))
            ->set('restart_date', $this->request->getPost('restart_date', Filter::FILTER_STRING, ''))
            ->set('restart_date', $this->request->getPost('restart_date', Filter::FILTER_STRING, ''))
            ->set('public_visibility', $this->request->getPost('public-visibility', Filter::FILTER_BOOL, false))
            ->set('public_votes', $this->request->getPost('public-votes', Filter::FILTER_BOOL, false))
            ->set('anon_can_vote', $this->request->getPost('anon-can-vote', Filter::FILTER_BOOL, false))
            ->set('who_can_add_track', $this->request->getPost('who-can-add-track', Filter::FILTER_STRING, ''))
            ->set('anon_votes_max_rating', $this->request->getPost('anon_votes_max_rating', Filter::FILTER_ABSINT, 0))
            ->set('user_votes_max_rating', $this->request->getPost('user_votes_max_rating', Filter::FILTER_ABSINT, 10))
            ->set('multiple_user_tracks', $this->request->getPost('multiple_user_tracks', Filter::FILTER_BOOL, false))
            ->set('multiple_anon_tracks', $this->request->getPost('multiple_anon_tracks', Filter::FILTER_BOOL, false))
            // @ToDo: Call the spotify service?
            ->set('spotify_playlist_public', $this->request->getPost('spotify_playlist_public', Filter::FILTER_BOOL, false))
            ->set('spotify_playlist_collaborative', $this->request->getPost('spotify_playlist_collaborative', Filter::FILTER_BOOL, false))
            ->set('spotify_playlist_uri', $this->request->getPost('spotify_playlist_uri', Filter::FILTER_STRING, ''))
            ->set('spotify_playlist_winner_uri', $this->request->getPost('spotify_playlist_winner_uri', Filter::FILTER_STRING, ''))
            ->set('spotify_playlist_historic_uri', $this->request->getPost('spotify_playlist_historic_uri', Filter::FILTER_STRING, ''))
        ;

        if (true !== $poll->validation() || true !== $poll->save()) {
            if (false === $poll->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating group');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $poll->getMessages());
        }

        return $this->response->sendApiContentCreated(
            $this->request->getContentType(),
            $this->format($this->method, $poll, $this->transformer, $this->resource)
        );
    }

    private function checkUserMembership($group_uuid): Organizations
    {
        $rtn = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));

        // Check who_can_create_polls field
        if ($rtn->member->rol === 'ROLE_ADMIN') {
            if ($rtn->org->get('who_can_create_polls') === 'OWNERS') {
                throw new UnauthorizedException('Only owners can create polls in this groups');
            }
        } elseif ($rtn->member->rol === 'ROLE_MEMBER') {
            if ($rtn->org->get('who_can_create_polls') !== 'MEMBERS') {
                throw new UnauthorizedException('Only admins and owners can create polls in this groups');
            }
        } elseif ($rtn->member->rol === 'ROLE_INVITED') {
            throw new UnauthorizedException('You should accept your invitation before create one new poll');
        } elseif ($rtn->member->rol !== 'ROLE_OWNER') {
            throw new InternalServerErrorException('Invalid role');
        }
        return $rtn->org;
    }
}
