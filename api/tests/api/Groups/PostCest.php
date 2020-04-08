<?php

namespace Discoveryfy\Tests\api\Groups;

use Page\Data;
use Phalcon\Api\Http\Response;
use Step\Api\Login;

class GroupsPostCest
{
    public function createGroupJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $group_data = Data::groupJson();
        $I->sendPOST(Data::$groupsUrl, $group_data);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::CREATED);
        $I->seeResponseMatchesJsonType([
            'type'                              => 'string',
            'id'                                => 'string',
            'attributes.created_at'             => 'string:date',
            'attributes.updated_at'             => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'                   => 'string',
            'attributes.description'            => 'string',
            'attributes.public_visibility'      => 'boolean',
            'attributes.public_membership'      => 'boolean',
            'attributes.who_can_create_polls'   => 'string',
            'links.self'                        => 'string:url',
        ]);
        $I->seeResponseContainsJson([
            'type'                              => 'groups',
            'attributes.updated_at'             => '',
            'attributes.name'                   => $group_data['name'],
            'attributes.description'            => $group_data['description'],
            'attributes.public_visibility'      => $group_data['public_visibility'],
            'attributes.public_membership'      => $group_data['public_membership'],
            'attributes.who_can_create_polls'   => $group_data['who_can_create_polls']
        ]);
        $group_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid];
    }

    public function createGroupJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $group_data = Data::groupJson();
        $I->sendPOST(Data::$groupsUrl, $group_data);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::CREATED);
        $I->seeResponseMatchesJsonType([
            'id'                            => 'string', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'created_at'                => 'string:date', //'2020-03-23 11:57:46'
//                'updated_at'                => 'string:date|null', //''
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ], '$.data');
        $I->seeSuccessJsonResponse('data', [
            'type'                          => 'groups',
            'attributes' => [
                'name'                      => $group_data['name'],
                'description'               => $group_data['description'],
                'public_visibility'         => $group_data['public_visibility'],
                'public_membership'         => $group_data['public_membership'],
                'who_can_create_polls'      => $group_data['who_can_create_polls'],
                'updated_at'                => '',
            ]
        ]);
        $group_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid];
    }
}