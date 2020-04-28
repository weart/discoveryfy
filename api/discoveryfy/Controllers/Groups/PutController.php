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
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
//use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;

/**
 * Modify one group
 *
 * Module       Groups
 * Class        PostApiController
 * OperationId  group.put (or group.modify)
 * Operation    PUT
 * OperationUrl /groups/{group_uuid}
 * Security     Only allowed to the owner or admins of the group
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{

    /** @var string */
    protected $model       = Organizations::class;

    /** @var string */
    protected $resource    = Relationships::GROUP;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    protected function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        $group_uuid = $parameters['id'];

        // Check if user is admin or owner of the group
        $org = $this->checkUserMembership($group_uuid);

        // Update group / organization information (Group is the public name, organization is the db name)
        $this->updateOrganization($org);

        // Return the object
        return $this->sendApiData($org);
    }

    private function checkUserMembership($group_uuid): Organizations
    {
        $rtn = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));

        if (!in_array($rtn->member->rol, ['ROLE_ADMIN', 'ROLE_OWNER'])) {
            throw new UnauthorizedException('Only admins and owners can modify a group');
        }
        return $rtn->org;
    }

    private function updateOrganization(Organizations $org): Organizations
    {
        $attrs = [
            'name'                  => Filter::FILTER_STRIPTAGS,
            'description'           => Filter::FILTER_STRIPTAGS,
            'public_visibility'     => Filter::FILTER_BOOL,
            'public_membership'     => Filter::FILTER_BOOL,
            'who_can_create_polls'  => Filter::FILTER_STRING,
        ];

        foreach ($attrs as $attr => $filter) {
            if ($this->request->hasPut($attr)) {
                $org->set($attr, $this->request->getPut($attr, $filter));
            }
        }

        if (true !== $org->validation() || true !== $org->save()) {
            if (false === $org->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing group');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $org->getMessages());
        }

        return $org;
    }
}
