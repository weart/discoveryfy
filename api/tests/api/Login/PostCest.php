<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Login;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class LoginPostCest
{
    private $invalid_user_msg = 'Wrong email/password combination'; //'Incorrect credentials'

    /**
     * INVALID LOGIN USER: Json / JsonApi
     */

    public function loginUnknownUserJson(Login $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $I->getLoginCSRFTokenJson());
        $I->setContentType('application/json');
        $I->sendPOST(Data::$loginUrl, [
            'username' => 'user',
            'password' => 'pass',
        ]);
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, $this->invalid_user_msg);
    }

    public function loginUnknownUserJsonApi(Login $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $I->getLoginCSRFTokenJsonApi());
        $I->setContentType('application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, [
            'username' => 'user',
            'password' => 'pass',
        ]);
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $this->invalid_user_msg);
    }

    public function loginWithoutCSRFJson(Login $I)
    {
//        $I->deleteHeader('X-CSRF-TOKEN');
        $I->setContentType('application/json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'CSRF not provided');
    }

    public function loginWithoutCSRFJsonApi(Login $I)
    {
//        $I->deleteHeader('X-CSRF-TOKEN');
        $I->setContentType('application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'CSRF not provided');
    }

    public function loginWithInvalidCSRFJson(Login $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', '1234');
        $I->setContentType('application/json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid CSRF token');
    }

    public function loginWithInvalidCSRFJsonApi(Login $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', '1234');
        $I->setContentType('application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Invalid CSRF token');
    }

    /**
     * LOGIN ANON: Json / JsonApi
     */
    public function loginAnonUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsAnon();
        $this->isValidAnonUser($I, $jwt, $session_id, $user_id);
    }

    public function loginAnonUserJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->getAuthTokenJsonApi();
        $this->isValidAnonUser($I, $jwt, $session_id, $user_id);
    }

    private function isValidAnonUser(Login $I, $jwt, $session_id, $user_id)
    {
        $I->assertNotEmpty($jwt, 'JWT token must be setted');
        $I->testJWTToken($jwt);
        $I->assertNotEmpty($session_id, 'session_id must be setted');
        $I->testUUID($session_id);
        $I->assertEmpty($user_id, 'Anon user must not have user_id');
    }

    /**
     * LOGIN USER: Json / JsonApi
     */

    public function loginKnownUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $this->isValidUser($I, $jwt, $session_id, $user_id);
    }

    public function loginKnownUserJsonApi(Login $I)
    {
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
        list($jwt, $session_id, $user_id) = $I->getAuthTokenJson(Data::loginJson());
        $this->isValidUser($I, $jwt, $session_id, $user_id);
    }

    private function isValidUser(Login $I, $jwt, $session_id, $user_id)
    {
        $I->assertNotEmpty($jwt, 'JWT token must be setted');
        $I->testJWTToken($jwt);
        $I->assertNotEmpty($session_id, 'session_id must be setted');
        $I->testUUID($session_id);
        $I->assertNotEmpty($user_id, 'user_id must be setted');
        $I->testUUID($user_id);
    }

    /**
     * LOGIN USER: Json / JsonApi - Test throttling
     *

    public function loginThrottlingJson(Login $I)
    {
        // First two attempts -> no delay
        $normal_time = time();
        for ($i = 0; $i < 2; $i++) {
            $this->loginUnknownUserJson($I);
        }
        $normal_time = time() - $normal_time; //seconds
        $normal_time /= 2; //average time per request
        $I->assertLessOrEquals(5, $normal_time, 'Normal time is very long...');


        // Attempts 3 & 4 -> 2 seconds delay
        $slow_time = time();
        for ($i = 0; $i < 2; $i++) {
            $this->loginUnknownUserJson($I);
        }
        $slow_time = time() - $slow_time; //seconds
        $slow_time /= 2; //average time per request
        $I->assertGreaterThan(($normal_time+1), $slow_time, 'Slow time should be at least one second slower');


        // More attempts -> 4 seconds delay
        $slower_time = time();
        for ($i = 0; $i < 2; $i++) {
            $this->loginUnknownUserJson($I);
        }
        $slower_time = time() - $slower_time;
        $slower_time /= 2; //average time per request
        $I->assertGreaterThan(($normal_time+3), $slower_time, 'Slower time should be at least 3 seconds slower');
    }
*/
}
