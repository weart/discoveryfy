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
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Phalcon\Api\Controllers\BaseCollectionApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Retrieves a list of Groups
 *
 * Module       Groups
 * Class        GetCollectionController
 * OperationId  groups.list
 * Operation    GET
 * OperationUrl /groups
 * Security     Logged user is part of the groups, or the groups have public visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetCollectionController extends BaseCollectionApiController
{
    /** @var string */
    protected $model       = Organizations::class;

    /** @var string */
    protected $resource    = Relationships::GROUP;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'collection';

    /** @var array */
    protected $includes = [
        Relationships::MEMBERSHIP,
        Relationships::POLL,
        Relationships::COMMENTS,
    ];

    public function checkSecurity($parameters): array
    {
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available to registered users');
//        }
        return $parameters;
    }

    protected function getRecords(array $parameters = [], string $orderBy = '', array $pagination = []): ResultsetInterface
    {
        $user_id = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
        return Organizations::getPublicVisibilityOrMemberGroups($user_id, $orderBy, $pagination);
    }
}
