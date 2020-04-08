<?php

namespace Discoveryfy\Tests\api\Groups;

use Page\Data;
use Phalcon\Api\Http\Response;
use Phalcon\Security\Random;
use Step\Api\Login;

class GroupsPutCest
{
    public function modifyGroupJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['name'] = 'test_'.(new Random())->hex(5);
        $modified_attrs['description'] = 'test_'.(new Random())->hex(5);
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'name' => $modified_attrs['name'],
            'description' => $modified_attrs['description']
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
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
            'attributes.name'                   => $modified_attrs['name'],
            'attributes.description'            => $modified_attrs['description'],
            'attributes.public_visibility'      => $modified_attrs['public_visibility'],
            'attributes.public_membership'      => $modified_attrs['public_membership'],
            'attributes.who_can_create_polls'   => $modified_attrs['who_can_create_polls']
        ]);

        //Leave group as before
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), $previous_attrs);
        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
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
            'attributes.name'                   => $previous_attrs['name'],
            'attributes.description'            => $previous_attrs['description'],
            'attributes.public_visibility'      => $previous_attrs['public_visibility'],
            'attributes.public_membership'      => $previous_attrs['public_membership'],
            'attributes.who_can_create_polls'   => $previous_attrs['who_can_create_polls']
        ]);
    }

    public function modifyGroupJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['name'] = 'test_'.(new Random())->hex(5);
        $modified_attrs['description'] = 'test_'.(new Random())->hex(5);
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'name' => $modified_attrs['name'],
            'description' => $modified_attrs['description']
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'id'                            => 'string', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'created_at'                => 'string:date', //'2020-03-23 11:57:46'
                'updated_at'                => 'string:date|null', //''
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ], '$.data');
        $I->seeSuccessJsonResponse('data', [
            'type'                          => 'groups',
            'attributes' => [
                'name'                      => $modified_attrs['name'],
                'description'               => $modified_attrs['description'],
                'public_visibility'         => $modified_attrs['public_visibility'],
                'public_membership'         => $modified_attrs['public_membership'],
                'who_can_create_polls'      => $modified_attrs['who_can_create_polls'],
            ]
        ]);

        //Leave group as before
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), $previous_attrs);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'id'                            => 'string', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'created_at'                => 'string:date', //'2020-03-23 11:57:46'
                'updated_at'                => 'string:date|null', //''
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ], '$.data');
        $I->seeSuccessJsonResponse('data', [
            'type'                          => 'groups',
            'attributes' => [
                'name'                      => $previous_attrs['name'],
                'description'               => $previous_attrs['description'],
                'public_visibility'         => $previous_attrs['public_visibility'],
                'public_membership'         => $previous_attrs['public_membership'],
                'who_can_create_polls'      => $previous_attrs['who_can_create_polls'],
            ]
        ]);
    }
}
