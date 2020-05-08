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
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Phalcon\Api\Controllers\BaseCollectionApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Retrieves a list of Tracks
 *
 * Module       PollsTracks
 * Class        GetCollectionController
 * OperationId  tracks.list
 * Operation    GET
 * OperationUrl /polls/{poll_uuid}/tracks
 * Security     Logged user is part of the group of the poll, or the poll has public visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetCollectionController extends BaseCollectionApiController
{
    /** @var string */
    protected $model       = Tracks::class;

    /** @var string */
    protected $resource    = Relationships::TRACK;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    /** @var array */
//    protected $includes = [
//        Relationships::MEMBERSHIP,
//        Relationships::POLL,
//        Relationships::COMMENTS,
//    ];

    public function checkSecurity($parameters): array
    {
        $user_uuid = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
        $rtn = Polls::isPublicVisibilityOrMember($parameters['id'], $user_uuid);
        if ($rtn->count() !== 1) {
            throw new UnauthorizedException('Only available when the group has public_visibility or you belong to the group');
        }
        return [
            'poll_id' => $parameters['id']
        ];
    }
}
