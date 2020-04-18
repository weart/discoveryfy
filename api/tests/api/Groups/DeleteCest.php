<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups;

use Page\Data;
use Codeception\Util\HttpCode;
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

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        $I->seeResponseEquals('');
    }


    public function deleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJsonApi($I);
        $I->comment('Deleting the group: '.$group_uuid);

        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
        $I->seeResponseEquals('');
    }
}
