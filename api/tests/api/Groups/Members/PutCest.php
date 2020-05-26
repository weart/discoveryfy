<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups\Members;

use Codeception\Exception\TestRuntimeException;
use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Page\Data;
use Step\Api\Login;

class GroupsMembersPutCest
{
    public function addAdminToTestGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        $this->addAdminToTestGroup($I, $groupsPost, 'application/json');
    }

    public function addAdminToTestGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        $this->addAdminToTestGroup($I, $groupsPost, 'application/vnd.api+json');
    }

    private function addAdminToTestGroup(Login $I, GroupsPostCest $groupsPost, string $contentType)
    {
        list($admin_jwt, $admin_session_id, $admin_user_id) = $I->loginAsAdmin();
        list($test_jwt, $test_session_id, $test_user_id, $group_id) = $groupsPost->createGroupAsTestJson($I); //loginAsTest
        $I->setContentType($contentType);

        // Test user invite Admin user into test group
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $admin_user_id), [ 'rol' => 'ROLE_INVITED' ]);
        $this->checkMembershipResponse($I, 'ROLE_INVITED', $contentType);

        // Admin user accepts the invitation of becoming part of test group
        $I->haveHttpHeader('Authorization', 'Bearer '.$admin_jwt);
        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $admin_user_id), [ 'rol' => 'ROLE_MEMBER' ]);
        $this->checkMembershipResponse($I, 'ROLE_MEMBER', $contentType);

        // Test user promote Admin user to group admin
//        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
//        $I->comment($test_jwt);
//        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $admin_user_id), [ 'rol' => 'ROLE_ADMIN' ]);
//        $this->checkMembershipResponse($I, 'ROLE_ADMIN', $contentType);

        // Test user downgrade Admin user to group member again
//        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
//        $I->comment($test_jwt);
//        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $admin_user_id), [ 'rol' => 'ROLE_MEMBER' ]);
//        $this->checkMembershipResponse($I, 'ROLE_MEMBER', $contentType);

        // Test user remove Admin user of test group
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendDELETE(sprintf(Data::$memberUrl, $group_id, $admin_user_id));
        $I->seeResponseIsValidDeleteJson();

//        $member_uuid = $this->grabMembershipUuid($I, $contentType);
//        $I->testUUID($member_uuid);
//
//        return [
//            $admin_jwt, $admin_session_id, $admin_user_id,
//            $test_jwt, $test_session_id, $test_user_id,
//            $group_id, $member_uuid
//        ];
    }

    private function checkMembershipResponse(Login $I, string $rol, string $contentType)
    {
        if ($contentType === 'application/json') {
            return $this->checkMembershipResponseJson($I, $rol);
        } else if ('application/vnd.api+json') {
            return $this->checkMembershipResponseJsonApi($I, $rol);
        }
        throw new TestRuntimeException('Invalid contentType');
    }

    private function checkMembershipResponseJson(Login $I, string $rol)
    {
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::memberResponseJsonType(), [
            'type'                      => 'memberships',
            'attributes.rol'            => $rol,
        ]);
    }

    private function checkMembershipResponseJsonApi(Login $I, string $rol)
    {
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::memberResponseJsonApiType(), [
            'type'                      => 'memberships',
            'attributes' => [
                'rol'                   => $rol,
//                'updated_at'            => '',
            ]
        ]);
    }

    private function grabMembershipUuid(Login $I, string $contentType)
    {
        if ($contentType === 'application/json') {
            return $I->grabDataFromResponseByJsonPath('$.id')[0];
        } else if ('application/vnd.api+json') {
            return $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        }
        throw new TestRuntimeException('Invalid contentType');
    }

    public function ownershipCantBeOrphanJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_id) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $user_id), [ 'rol' => 'ROLE_MEMBER' ]);

        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Without enough permissions/Invalid call');
    }

    public function ownershipCantBeOrphanJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_id) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendPUT(sprintf(Data::$memberUrl, $group_id, $user_id), [ 'rol' => 'ROLE_MEMBER' ]);

        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Without enough permissions/Invalid call');
    }

//    public function ownershipCanBeTransfer(Login $I, GroupsPostCest $groupsPost)
//    {
//        //Group owner
//    }

//    public function authorizeAnonPublicMembership(Login $I, GroupsPostCest $groupsPost)
//    public function authorizeUserPublicMembership(Login $I, GroupsPostCest $groupsPost)
//    public function authorizeInvitationMembership(Login $I, GroupsPostCest $groupsPost)
//    public function unauthorizePrivateMembership(Login $I, GroupsPostCest $groupsPost)
//    public function unauthorizeAnonMembership(Login $I, GroupsPostCest $groupsPost)
}
