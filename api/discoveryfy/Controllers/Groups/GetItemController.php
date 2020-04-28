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
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseItemApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;

/**
 * Retrieves a Group
 *
 * Module       Groups
 * Class        GetItemController
 * OperationId  group.get
 * Operation    GET
 * OperationUrl /groups/{group_uuid}
 * Security     Logged user is part of the group, or the group has public visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetItemController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Organizations::class;

    /** @var string */
    protected $resource    = Relationships::GROUP;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

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

    protected function findRecord(array $parameters)
    {
        $user_id = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
        $rtn = Organizations::isPublicVisibilityOrMember($parameters['id'], $user_id);
        if ($rtn->count() !== 1) {
            throw new UnauthorizedException('Only available when the group has public_visibility or you belong to the group');
        }
        return $rtn->org;
    }
}
