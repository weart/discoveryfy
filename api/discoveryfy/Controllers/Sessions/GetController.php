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
use Phalcon\Api\Controllers\BaseController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;

/**
 * Get one session
 *
 * Module       Sessions
 * Class        GetController
 * OperationId  session.get
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetController extends BaseController
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
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available for registered users');
//        }
        if ($this->auth->getSession()->get('id') !== $session_uuid && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('Session unauthorized for this action');
        }
        return parent::callAction($session_uuid);
    }
}
