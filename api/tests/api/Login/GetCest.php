<?php

namespace Discoveryfy\Tests\api\Login;

use ApiTester;
use Page\Data;
use Phalcon\Api\Http\Response;
//use function json_decode;

class GetCest
{
    public function getCSRFTokenJson(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET(Data::$loginUrl);
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
    }

    public function getCSRFTokenJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendGET(Data::$loginUrl);
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseIsJsonApiSuccessful();
        $I->seeResponseMatchesJsonType([
            'type' => 'string',
            'id' => 'string'
        ], '$.data');
        $I->seeResponseContainsJson(['type' => 'CSRF']);
    }
}
