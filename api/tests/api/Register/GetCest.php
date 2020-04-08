<?php

namespace Discoveryfy\Tests\api\Register;

use Page\Data;
use Phalcon\Api\Http\Response;
use Step\Api\Login;

class RegisterGetCest
{
    public function getRegisterCSRFTokenJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(Data::$registerUrl);
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $csrf = trim($I->grabResponse(), '"');
        $I->testCSRFToken($csrf);
        return $csrf;
    }

    public function getRegisterCSRFTokenJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$registerUrl);
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseIsJsonApiSuccessful();
        $I->seeResponseMatchesJsonType([
            'type' => 'string',
            'id' => 'string'
        ], '$.data');
        $I->seeResponseContainsJson(['type' => 'CSRF']);
        $csrf = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        $I->testCSRFToken($csrf);
        return $csrf;
    }
}
