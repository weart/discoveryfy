<?php

namespace Discoveryfy\Tests\api;

use ApiTester;
use Page\Data;

class NotFoundCest
{
    public function checkNotFoundRouteJson(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET(Data::$wrongUrl);
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson(["errors"=>["404 (Not Found)"]]);
    }
    public function checkNotFoundRouteJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendGET(Data::$wrongUrl);
        $I->seeResponseIs404();
    }
}
