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
use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Phalcon\Api\Controllers\BaseItemApiController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Db\Column;

/**
 * Retrieves a Track
 *
 * Module       Groups
 * Class        GetItemController
 * OperationId  track.get
 * Operation    GET
 * OperationUrl /polls/{poll_uuid}/tracks/{track_uuid}
 * Security     Logged user is part of the group of the poll, or the poll has public visibility
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetItemController extends BaseItemApiController
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
//        Relationships::COMMENTS,
//    ];

    public function checkSecurity($parameters): array
    {
        $poll_uuid = $parameters['id'];
        $track_uuid = $parameters['sub.id'];

        $user_uuid = $this->auth->getUser() ? $this->auth->getUser()->get('id') : null;
        $rtn = Polls::isPublicVisibilityOrMember($poll_uuid, $user_uuid);
        if ($rtn->count() !== 1) {
            throw new UnauthorizedException('Only available when the group has public_visibility or you belong to the group');
        }
        return [
            'id' => $track_uuid,
            'poll_id' => $poll_uuid
        ];
    }

//    protected function findRecord(array $parameters)
//    {
//        $poll_uuid = $parameters['id'];
//        $track_uuid = $parameters['sub.id'];
//        return $this->getTrack($poll_uuid, $track_uuid);
//    }
//
//    protected function getTrack(string $poll_uuid, string $track_uuid): Tracks
//    {
//        return Tracks::findFirst([
//            'conditions' => 'id = :track_id: AND poll_id = :poll_id:',
//            'bind'       => [ 'track_id' => $track_uuid,            'poll_id' => $poll_uuid ],
//            'bindTypes'  => [ 'track_id' => Column::BIND_PARAM_STR, 'poll_id' => Column::BIND_PARAM_STR ],
//        ]);
//    }
}
