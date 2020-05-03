<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Register;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class RegisterGetCest
{
    public function getRegisterCSRFTokenJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(Data::$registerUrl);
        $I->seeResponseIsValidJson();
        $csrf = trim($I->grabResponse(), '"');
        $I->testCSRFToken($csrf);
        return $csrf;
    }

    public function getRegisterCSRFTokenJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$registerUrl);
        $I->seeResponseIsValidJsonApi(
            HttpCode::OK,
            [
                'type' => 'string:!empty',
                'id' => 'string:!empty'
            ],
            [
                'type' => 'CSRF'
            ]
        );
        $csrf = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        $I->testCSRFToken($csrf);
        return $csrf;
    }
}
