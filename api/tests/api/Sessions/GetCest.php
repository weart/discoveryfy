<?php

namespace Discoveryfy\Tests\api\Sessions;

use Page\Data;
use Phalcon\Api\Http\Response;
use Step\Api\Login;

class SessionsGetCest
{
    public function getSessionJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string',
            'id'                    => 'string',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'sessions']);
    }

    public function getSessionJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

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
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseMatchesJsonType([
            'created_at'    => 'string:date',
            'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
            'name'       => 'string',
        ], '$.data.attributes');
        $I->seeResponseMatchesJsonType([
            'self'          => 'string:url',
        ], '$.data.links');
    }
}
