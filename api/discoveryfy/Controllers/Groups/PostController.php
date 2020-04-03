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
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Phalcon\Api\Controllers\BaseController;
//use Phalcon\Mvc\Controller;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
use Phalcon\Security\Random;

/**
 * Create one new group
 *
 * Module       Groups
 * Class        PostController
 * OperationId  group.create
 * Operation    POST
 * OperationUrl /groups
 * Security     Only allowed to logged users
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends BaseItemApiController
{
    use FractalTrait;

    /** @var string */
//    protected $model       = Organizations::class;

    /** @var string */
    protected $resource    = Relationships::GROUP;

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

        $this->db->begin();

        // @ToDo: Double filtering, here and in the setter, maybe one can be removed?
        // @see: Memberships as an internal implementation of Organizations?
        // https://forum.phalcon.io/discussion/81/save-related-records-oncreate-in-transaction-like-way
        $org = new Organizations();
        $org
            ->set('id', (new Random())->uuid())
            ->set('name', $this->request->getPost('name', Filter::FILTER_STRIPTAGS))
            ->set('description', $this->request->getPost('description', Filter::FILTER_STRIPTAGS))
            ->set('public_visibility', $this->request->getPost('public_visibility', Filter::FILTER_BOOL, false))
            ->set('public_membership', $this->request->getPost('public_membership', Filter::FILTER_BOOL, false))
            ->set('who_can_create_polls', $this->request->getPost('who_can_create_polls', Filter::FILTER_STRIPTAGS, 'OWNERS'))
        ;

        $member = new Memberships();
        $member
            ->set('id', (new Random())->uuid())
            ->set('user_id', $this->auth->getUser()->get('id'))
            ->set('organization_id', $org->get('id'))
            ->set('rol', 'ROLE_OWNER')
        ;

        if (true !== $org->validation() || true !== $member->validation() || true !== $org->save() || true !== $member->save()) {
            $this->db->rollback();
            if (false === $org->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating group');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $org->getMessages());
        }

        $this->db->commit();

        return $this->response->sendApiContentCreated(
            $this->request->getContentType(),
            $this->format($this->method, $org, $this->transformer, $this->resource)
        );
    }
}
