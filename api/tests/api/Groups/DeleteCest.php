<?php

namespace Discoveryfy\Tests\api\Groups;

use Page\Data;
use Phalcon\Api\Http\Response;
use Step\Api\Login;

class GroupsDeleteCest
{
    public function deleteGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupJson($I);
        $I->comment('Deleting the group: '.$group_uuid);

        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseCodeIs(Response::NO_CONTENT);
        $I->seeResponseEquals('');
    }


    public function deleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupJsonApi($I);
        $I->comment('Deleting the group: '.$group_uuid);

        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));

        $I->seeResponseCodeIs(Response::NO_CONTENT);
        $I->seeResponseEquals('');
    }
}
