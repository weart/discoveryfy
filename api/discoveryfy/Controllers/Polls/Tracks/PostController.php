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
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Db\Column;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
use Phalcon\Security\Random;

/**
 * Create one new track
 *
 * Module       PollsTracks
 * Class        PostController
 * OperationId  track.create
 * Operation    POST
 * OperationUrl /polls/{poll_uuid}/tracks
 * Security     Check poll->has who_can_add_track & membership
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends BaseItemApiController
{
    use FractalTrait;

    /** @var string */
    protected $model       = Tracks::class;

    /** @var string */
    protected $resource    = Relationships::TRACK;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    protected function checkSecurity(array $parameters): array
    {
//        $user_uuid = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
//        $res = Polls::isPublicVisibilityOrMember($parameters['id'], $user_uuid);
//        if ($res->count() != 1) {
//            throw new UnauthorizedException('Only available when the group has public_membership or you belong to the group');
//        }
//        $poll = $res->getFirst();

        $poll = $this->getPoll($parameters['id']);
        if (!$this->auth->getUser()) {
            if ($poll->get('who_can_add_track') !== 'ANYONE') {
                throw new UnauthorizedException('Only available to registered users');
            }
        } elseif ($poll->get('who_can_add_track') !== 'USERS') {
            $membership = $this->getMembership($poll->get('organization_id'), $this->auth->getUser()->get('id'));
            if (!$membership) {
                throw new UnauthorizedException('Only available when you belong to the group');
            }
            if ($poll->get('who_can_add_track') === 'ADMINS' && !in_array($membership->get('rol'), ['ROLE_OWNER', 'ROLE_ADMIN'])) {
                throw new UnauthorizedException('Only available to group admins and owner');
            } else if ($poll->get('who_can_add_track') === 'OWNERS' && $membership->get('rol') !== 'ROLE_OWNER') {
                throw new UnauthorizedException('Only available to group owner');
            }
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        if (empty($this->request->getPost())) {
            throw new BadRequestException('Empty post');
        }

        $track = new Tracks();
        $track
            ->set('id', (new Random())->uuid())
            ->set('poll_id', $parameters['id'])
            ->set('session_id', $this->auth->getSession()->get('id'))
            ->set('artist', $this->request->getPost('artist', Filter::FILTER_STRING))
            ->set('name', $this->request->getPost('name', Filter::FILTER_STRING))
            ->set('spotify_uri', $this->request->getPost('spotify_uri', Filter::FILTER_STRING))
            ->set('youtube_uri', $this->request->getPost('youtube_uri', Filter::FILTER_STRING))
        ;
        if ($this->auth->getUser()) {
            $track->set('user_id', $this->auth->getUser()->get('id'));
        }

        if (true !== $track->validation() || true !== $track->save()) {
            if (false === $track->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating track');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $track->getMessages());
        }

        return $this->response->sendApiContentCreated(
            $this->request->getContentType(),
            $this->format($this->method, $track, $this->transformer, $this->resource)
        );
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
}
