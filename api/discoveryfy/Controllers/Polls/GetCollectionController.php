<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Polls;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Phalcon\Api\Controllers\BaseCollectionApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Retrieves a list of Polls
 *
 * Module       Polls
 * Class        GetCollectionController
 * OperationId  polls.list
 * Operation    GET
 * OperationUrl /polls
 * Security     Logged user is part of the group of the poll, or the poll has public visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetCollectionController extends BaseCollectionApiController
{
    /** @var string */
    protected $model       = Polls::class;

    /** @var string */
    protected $resource    = Relationships::POLL;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'collection';

    /** @var array */
//    protected $includes = [
//        Relationships::MEMBERSHIP,
//        Relationships::POLL,
//        Relationships::COMMENTS,
//    ];

    public function checkSecurity($parameters): array
    {
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available for registered users');
//        }
        return $parameters;
    }

    protected function getRecords(array $parameters = [], string $orderBy = ''): ResultsetInterface
    {
        $user_id = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
        return Polls::getPublicVisibilityOrMember($user_id, $orderBy);
    }
}
