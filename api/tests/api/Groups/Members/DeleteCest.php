<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups\Members;

use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Page\Data;
use Step\Api\Login;

class GroupsMembersDeleteCest
{
    public function deleteMemberJson(Login $I, GroupsMembersPutCest $membersPut, GroupsPostCest $groupsPost)
    {
        $I->comment('Deleting the membership tested in PutCest');
//        list(
//            $admin_jwt, $admin_session_id, $admin_user_id,
//            $test_jwt, $test_session_id, $test_user_id,
//            $group_uuid, $member_uuid
//        ) = $membersPut->addAdminToTestGroupJson($I, $groupsPost);
//        $I->comment('Deleting the membership: '.$member_uuid);
//
//        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
//        $I->sendDELETE(sprintf(Data::$memberUrl, $group_uuid, $admin_user_id));
//
//        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
//        $I->seeResponseEquals('');
    }

//    public function deleteMemberJsonApi(Login $I, GroupsMembersPutCest $membersPut, GroupsPostCest $groupsPost)
//    {
//        list(
//            $admin_jwt, $admin_session_id, $admin_user_id,
//            $test_jwt, $test_session_id, $test_user_id,
//            $group_uuid, $member_uuid
//            ) = $membersPut->addAdminToTestGroupJsonApi($I, $groupsPost);
//        $I->comment('Deleting the membership: '.$member_uuid);
//
//        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
//        $I->sendDELETE(sprintf(Data::$memberUrl, $group_uuid, $admin_user_id));
//
//        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
//        $I->seeResponseEquals('');
//    }
}
