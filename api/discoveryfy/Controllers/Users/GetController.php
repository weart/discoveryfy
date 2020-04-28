<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Users;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;

/**
 * Retrieves a User
 *
 * Module       Users
 * Class        GetController
 * OperationId  user.get
 * Operation    GET
 * OperationUrl /users/{user_uuid}
 * Security     Only the current logged user or app admin
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USER;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    public function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }
        if ($this->auth->getUser()->get('id') !== $parameters['id'] && !$this->auth->getUser()->isAdmin()) {
            throw new UnauthorizedException('User unauthorized for this action');
        }
        return $parameters;
    }

    public function getRecord(array $parameters)
    {
        return $this->auth->getUser();
    }
}
