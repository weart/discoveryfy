<?php

namespace Discoveryfy\Tests\api;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class NotFoundCest
{
    private $not_found_msg = '404 (Not Found)';

    public function anyNotFoundRouteJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function anyNotFoundRouteJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonApiError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function anonNotFoundRouteJson(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function anonNotFoundRouteJsonApi(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$wrongUrl);

        $I->seeResponseIsJsonApiError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }
}
