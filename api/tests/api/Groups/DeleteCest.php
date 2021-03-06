<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class GroupsDeleteCest
{
    public function deleteGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        $I->comment('Deleting the group: '.$group_uuid);

        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsValidDeleteJson();
    }


    public function deleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJsonApi($I);
        $I->comment('Deleting the group: '.$group_uuid);

        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsValidDeleteJsonApi();
    }

    public function anonCantDeleteGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only available to registered users');
    }

    public function anonCantDeleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJsonApi($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only available to registered users');
    }

    public function userCantDeleteGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($user_jwt, $user_session_id, $user_user_id) = $I->loginAsSecondaryTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$user_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'This user not belong to this group');
    }

    public function userCantDeleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJsonApi($I);
        list($user_jwt, $user_session_id, $user_user_id) = $I->loginAsSecondaryTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$user_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'This user not belong to this group');
    }
}
