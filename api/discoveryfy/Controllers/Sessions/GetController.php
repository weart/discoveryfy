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
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Sessions;
use Phalcon\Api\Controllers\BaseItemApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;

/**
 * Get one session
 *
 * Module       Sessions
 * Class        ApiController
 * OperationId  session.get
 * Operation    GET
 * OperationUrl /sessions/{session_uuid}
 * Security     Only the current logged session or app admin
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetController extends BaseItemApiController
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
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available to registered users');
//        }
        if ($this->auth->getSession()->get('id') !== $parameters['id'] && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('Session unauthorized for this action');
        }
        return $parameters;
    }

    public function findRecord(array $parameters)
    {
        return $this->auth->getSession();
    }
}
