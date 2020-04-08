<?php

namespace Discoveryfy\Tests\api\Users;

use Page\Data;
use Phalcon\Api\Http\Response;
use Phalcon\Security\Random;
use Step\Api\Login;

class UsersPutCest
{
    public function modifyUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $session_id));
        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.username"]');

        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);

//        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$usersUrl, $user_id), [
            'username' => $new_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string',
            'id'                    => 'string',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.username'   => 'string',
            'attributes.email'      => 'string:email',
            'attributes.language'   => 'string',
            'attributes.theme'      => 'string',
            'attributes.rol'        => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'users']);
        $I->seeResponseContainsJson(['attributes.username' => $new_name]);
    }

    public function modifyUserJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));

        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.username"]');
        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);

//        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$usersUrl, $user_id), [
            'username' => $new_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'        => 'error'
        ]);
        $I->seeResponseIsJsonApiSuccessful();
        $I->seeResponseMatchesJsonType([
            'type'          => 'string',
            'id'            => 'string',
            'attributes'    => 'array',
            'links'         => 'array',
        ], '$.data');
        $I->seeResponseContainsJson(['type' => 'users']);
        $I->seeResponseMatchesJsonType([
            'created_at'    => 'string:date',
            'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
            'username'      => 'string',
            'email'         => 'string:email',
            'language'      => 'string',
            'theme'         => 'string',
            'rol'           => 'string',
        ], '$.data.attributes');
        $I->seeResponseContainsJson(['theme' => 'default']);
        $I->seeResponseContainsJson(['rol' => 'ROLE_USER']);
        $I->seeResponseMatchesJsonType([
            'self'          => 'string:url',
        ], '$.data.links');

        $I->seeResponseContainsJson(['username' => $new_name]);
    }
}
