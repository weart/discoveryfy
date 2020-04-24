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
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Tracks;
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
 * Create one new track
 *
 * Module       PollsTracks
 * Class        PostController
 * OperationId  track.create
 * Operation    POST
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}
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
        // @ToDo
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available for registered users');
//        }
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
            ->set('artist', $this->request->getPost('artist', Filter::FILTER_STRING))
            ->set('name', $this->request->getPost('name', Filter::FILTER_STRING))
            ->set('spotify_uri', $this->request->getPost('spotify_uri', Filter::FILTER_STRING))
            ->set('youtube_uri', $this->request->getPost('youtube_uri', Filter::FILTER_STRING))
        ;

        if (true !== $track->validation() || true !== $track->validation()) {
            if (false === $track->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating track');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $track->getMessages());
        }

        $this->db->commit();

        return $this->response->sendApiContentCreated(
            $this->request->getContentType(),
            $this->format($this->method, $track, $this->transformer, $this->resource)
        );
    }
}
