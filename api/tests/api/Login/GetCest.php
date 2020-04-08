<?php

namespace Discoveryfy\Tests\api\Login;

use Step\Api\Login;

class LoginGetCest
{
    public function getCSRFTokenJson(Login $I)
    {
        $I->testCSRFToken($I->getLoginCSRFTokenJson());
    }

    public function getCSRFTokenJsonApi(Login $I)
    {
        $I->testCSRFToken($I->getLoginCSRFTokenJson());
    }
}
