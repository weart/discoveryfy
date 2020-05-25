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

class GroupsMembersGetCollectionCest
{
    private $unauthorized_msg = 'Only available to registered users';

    public function memberGetGroupsMembersJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$membersUrl, $group_uuid));
        $I->seeCollectionResponseIsJsonSuccessful(HttpCode::OK, Data::memberResponseJsonType(), [
            'type'  => 'memberships'
        ]);
        $this->anonCantGetGroupsMembersJson($I, $group_uuid);
    }

    private function anonCantGetGroupsMembersJson(Login $I, string $group_uuid)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$membersUrl, $group_uuid));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }

    public function memberGetGroupsMembersJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$membersUrl, $group_uuid));
        $I->seeCollectionResponseIsJsonApiSuccessful(HttpCode::OK, Data::memberResponseJsonApiType(), [
            'type'  => 'memberships',
        ]);

        $this->anonCantGetGroupsMembersJsonApi($I, $group_uuid);
    }

    private function anonCantGetGroupsMembersJsonApi(Login $I, string $group_uuid)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$membersUrl, $group_uuid));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }
}
