<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
