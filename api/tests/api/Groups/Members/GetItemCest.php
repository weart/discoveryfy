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

class GroupsMembersGetItemCest
{
    private $unauthorized_msg = 'Only available to registered users';

    public function anonGetPublicGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($test_jwt, $test_session_id, $test_user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();

        // anon user can't see user group membership
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        // external user can't see user group membership
        $I->setContentType('application/json');
        $I->deleteHeader('Authorization');
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid Token');

        // test user can see own membership
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::memberResponseJsonType(), [
            'type'              => 'memberships',
            'attributes.rol'    => 'ROLE_OWNER',
        ]);
    }

    public function anonGetPublicGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($test_jwt, $test_session_id, $test_user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();

        // anon user can't see user group membership
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        // external user can't see user group membership
        $I->setContentType('application/vnd.api+json');
        $I->deleteHeader('Authorization');
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Invalid Token');

        // test user can see own membership
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$memberUrl, $group_uuid, $test_user_id));
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::memberResponseJsonApiType(), [
            'type'          => 'memberships',
            'attributes'    =>
            [
                'rol'       => 'ROLE_OWNER',
            ]
        ]);
    }
}
