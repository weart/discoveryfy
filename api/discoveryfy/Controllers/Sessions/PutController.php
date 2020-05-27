<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Sessions;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Sessions;
use Discoveryfy\Validators\UuidValidator;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;

/**
 * Replaces the name of the Session
 *
 * Module       Sessions
 * Class        PutController
 * OperationId  session.put
 * Operation    PUT
 * OperationUrl /sessions/{session_uuid}
 * Security     Only the current logged session or app admin
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Sessions::class;

    /** @var string */
    protected $resource    = Relationships::SESSION;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    public function checkSecurity(array $parameters): array
    {
        if ($this->auth->getSession()->get('id') !== $parameters['id'] && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('Session unauthorized for this action');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        if (!$this->request->hasPut('name')) {
            throw new BadRequestException('Undefined name');
        }
        $this->auth->getSession()->set('name', $this->request->getPut('name', Filter::FILTER_STRING));

        if (true !== $this->auth->getSession()->validation() || true !== $this->auth->getSession()->save()) {
            if (false === $this->auth->getSession()->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing session name');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $this->auth->getSession()->getMessages());
        }

        return $this->sendApiData($this->auth->getSession());
    }
}
