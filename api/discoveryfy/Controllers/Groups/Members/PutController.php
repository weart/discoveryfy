<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups\Members;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
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
use Phalcon\Security\Random;

/**
 * Modify the rol of an User in a Group
 *
 * Module       GroupsMembers
 * Class        PutApiController
 * OperationId  member.put (or member.modify)
 * Operation    PUT
 * OperationUrl /groups/{group_uuid}/members/{user_uuid}
 * Security     Or the group has public membership and can become a member, or a member of the group invite/promote a user
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{
    /** @var string */
//    protected $model       = Memberships::class;

    /** @var string */
    protected $resource    = Relationships::MEMBERSHIP;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    protected function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        $group_uuid = $parameters['id'];
        $user_uuid = $parameters['sub.id'];

        $rol = $this->request->getPut('rol', Filter::FILTER_STRING, 'ROLE_INVITED');
        if (!in_array($rol, ['ROLE_OWNER', 'ROLE_ADMIN', 'ROLE_MEMBER','ROLE_INVITED'])) {
            throw new BadRequestException('Invalid rol');
        }
        if (false === ($org = Organizations::findById($group_uuid))) {
            throw new BadRequestException('Invalid group uuid');
        }

        $security_pass = false;

        try {
            $requester = Organizations::getUserMembership($group_uuid, $this->auth->getUser()->get('id'));
        } catch (UnauthorizedException $e) {
            $requester = false;
        }

        // Actions by and for the requester:
        if ($user_uuid === $this->auth->getUser()->get('id')) {
            if ($rol !== 'ROLE_MEMBER') {
                throw new UnauthorizedException('The unique valid rol is ROLE_MEMBER');
            }
            $target = $requester;
            // One user become part of one public group
            if ($requester === false && $org->get('public_membership')) {
                $security_pass = true;

            // One user accept an invitation
            } else if ($requester !== false && $requester->member->rol === 'ROLE_INVITED') {
                $security_pass = true;
            }
        // User part of an organization send an invitation or promote a member
        } else {
            if ($requester === false || $requester->member->rol === 'ROLE_INVITED') {
                throw new UnauthorizedException('Only members can execute this call');
            }
            try {
                $target = Organizations::getUserMembership($group_uuid, $user_uuid);
            } catch (UnauthorizedException $e) {
                $target = false;
            }
            // Anyone in a group can invite new members
            if ($rol === 'ROLE_INVITED' && $target === false) {
                $security_pass = true;
            }
            // Owners and admins can promote users
            if (
                in_array($requester->member->rol, ['ROLE_OWNER', 'ROLE_ADMIN']) && $rol === 'ROLE_ADMIN'
                && $target !== false && $target->member->rol !== 'ROLE_OWNER'
            ) {
                $security_pass = true;
            }
            // Owners can transfer the ownership
            if ($requester->member->rol === 'ROLE_OWNER' && $target !== false && $target !== 'ROLE_INVITED') {
                $this->changeGroupOwner($group_uuid, $this->auth->getUser()->get('id'), $user_uuid);

                return $this->response->sendNoContent();
            }
        }
        if (!$security_pass) {
            throw new UnauthorizedException('Without enough permissions/Invalid call');
        }

        $this->logger->addInfo(sprintf(
            'Membership update: User %s with rol %s updated the user %s with rol %s to %s',
            $this->auth->getUser()->get('id'),
            (($requester !== false) ? $requester->member->rol : 'none'),
            $user_uuid,
            ((isset($target) && $target !== false) ? $target->member->rol : 'none'),
            $rol
        ));
        $membership = $this->updateMembership($group_uuid, $user_uuid, $rol);

        if ($membership instanceof Response) {
            return $membership;
        }
        return $this->sendApiData($membership);
    }

    private function updateMembership(string $org_uuid, string $user_uuid, string $rol)
    {
        // Check if the membership exist
        $membership = $this->getMembership($org_uuid, $user_uuid);
        if ($membership) {
            if ($membership->get('rol') !== $rol) {
                $membership->set('rol', $rol);
            }
        } else {
            $membership = (new Memberships())
                ->set('id', (new Random())->uuid())
                ->set('user_id', $user_uuid)
                ->set('organization_id', $org_uuid)
                ->set('rol', $rol)
            ;
        }
        if (true !== $membership->validation() || true !== $membership->save()) {
            if (false === $membership->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating membership');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $membership->getMessages());
        }
        return $membership;
    }

    private function changeGroupOwner(string $org_uuid, string $prev_owner_uuid, string $new_owner_uuid)
    {

        $prev_owner = $this->getMembership($org_uuid, $prev_owner_uuid);
        if (!$prev_owner || $prev_owner->get('rol') !== 'ROLE_OWNER') {
            throw new UnauthorizedException('Only the owner can do that');
        }
        $new_owner = $this->getMembership($org_uuid, $new_owner_uuid);
        if (!$new_owner) {
            throw new UnauthorizedException('Only members of a group can become owners');
        }

        $this->db->begin();
        $prev_owner->set('rol', 'ROLE_ADMIN');
        $new_owner->set('rol', 'ROLE_OWNER');
        if (true !== $prev_owner->validation() || true !== $new_owner->validation() ||
            true !== $prev_owner->save() || true !== $new_owner->save()
        ) {
            $this->db->rollback();
            throw new InternalServerErrorException('Error in the validation changing group ownership');
        }

        $this->db->commit();
    }

    private function getMembership(string $org_uuid, string $user_uuid)
    {
        return Memberships::findFirst([
            'conditions' => 'user_id = :user_id: AND organization_id = :organization_id:',
            'bind'       => [
                'user_id' => $user_uuid,
                'organization_id' => $org_uuid,
            ],
            'bindTypes'  => [
                'user_id' => \Phalcon\Db\Column::BIND_PARAM_STR,
                'organization_id' => \Phalcon\Db\Column::BIND_PARAM_STR,
            ],
        ]);
    }
}
