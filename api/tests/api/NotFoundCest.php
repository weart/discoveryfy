<?php

namespace Discoveryfy\Tests\api;

use ApiTester;
use Page\Data;
use Codeception\Util\HttpCode;

class NotFoundCest
{
    public function checkNotFoundRouteJson(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonSuccessful(HttpCode::NOT_FOUND);
        $I->seeResponseContainsJson([
            "errors" => [
                [
                    'status' => HttpCode::NOT_FOUND,
                    'code' => HttpCode::NOT_FOUND,
                    'title' => '404 (Not Found)'
                ]
            ]
        ]);
    }

    public function checkNotFoundRouteJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonSuccessful(HttpCode::NOT_FOUND);
        $I->seeResponseIs404();
    }
}
