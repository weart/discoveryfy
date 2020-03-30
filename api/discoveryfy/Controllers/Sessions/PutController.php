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
use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;

/**
 * Replaces the name of the Session
 *
 * Module       Sessions
 * Class        PostController
 * OperationId  session.put
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseController
{
    /** @var string */
    protected $model       = Sessions::class;

    /** @var string */
    protected $resource    = Relationships::SESSION;

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /** @var string */
    protected $method = 'item';

    public function callAction(string $session_uuid = ''): ResponseInterface
    {
        if ($this->auth->getSession()->get('id') !== $session_uuid && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('Session unauthorized for this action');
        }
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

        return parent::callAction($session_uuid);
    }
}
