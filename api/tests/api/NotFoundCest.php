<?php

namespace Discoveryfy\Tests\api;

use Page\Data;
use Codeception\Util\HttpCode;
use Step\Api\Login;

class NotFoundCest
{
    public function checkNotFoundRouteJson(Login $I)
    {
        $I->setContentType('application/json');
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

    public function checkNotFoundRouteJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonSuccessful(HttpCode::NOT_FOUND);
        $I->seeResponseIs404();
    }
}
