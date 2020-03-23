<?php

namespace Discoveryfy\Tests\api\Login;

use ApiTester;
//use Discoveryfy\Models\Users;
use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Api\Http\Response;

class LoginPostCest
{
    /**
     * INVALID LOGIN USER: Json / JsonApi
     */

    public function loginUnknownUserJson(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I)); //Used first, headers are unsetted after
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPOST(Data::$loginUrl, [
            'username' => 'user',
            'password' => 'pass',
        ]);
        $I->seeResponseIsJsonSuccessful(HttpCode::BAD_REQUEST);
        $I->seeSuccessJsonResponse('errors', [
            [
                'code' => HttpCode::BAD_REQUEST,
                'status' => HttpCode::BAD_REQUEST,
                'title' => 'Wrong email/password combination' //'Incorrect credentials'
            ]
        ]);
    }

    public function loginUnknownUserJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, [
            'username' => 'user',
            'password' => 'pass',
        ]);
        $title = 'Wrong email/password combination'; //$msg = 'Incorrect credentials';
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $title);
    }

    /**
     * LOGIN ANON: Json / JsonApi
     */
    public function loginAnonUserJson(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPOST(Data::$loginUrl);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseContainsJson(['type' => 'jwt']);
        $I->seeResponseContainsJson(['type' => 'session']);
    }

    public function loginAnonUserJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl);
        $I->seeResponseCodeIs(Response::OK);
        $I->cantSeeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson(['type' => 'jwt']);
        $I->seeResponseContainsJson(['type' => 'session']);
    }

    /**
     * LOGIN USER: Json / JsonApi
     */

    public function loginKnownUserJson(ApiTester $I)
    {
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseCodeIs(Response::OK);
        $I->cantSeeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        //@TODO: Improve testing, the format will be [ [jwt],[session]{,[user]} ]
//        $I->seeResponseContainsJson([
//            'jwt' => 'string',
//            'session' => 'array',
//        ]);
//        $I->seeResponseMatchesJsonType([
//            'type' => 'string',
//            'id' => 'string'
//        ], '$..');
//        Step  See response matches json type {"type":"session"},"$[*].type"
//         Fail  Key `type` doesn't exist in ["jwt","session","user"]
//        $I->seeResponseMatchesJsonType([
//            'type' => 'jwt',
//            'type' => 'session',
//            'type' => 'user'
//        ], '$[*]');
//        $I->seeResponseContainsJson([
//            ['type' => 'jwt'],
//            ['type' => 'session'],
//            ['type' => 'user'],
//        ]);
//        $I->seeResponseContainsJson(['type' => 'session']);
//        $I->seeResponseContainsJson(['type' => 'user']);

        // response: {name: john, email: john@gmail.com}
//        $I->seeResponseContainsJson(array('name' => 'john'));
        $I->seeResponseContainsJson(['type' => 'jwt']);
        $I->seeResponseContainsJson(['type' => 'session']);
        $I->seeResponseContainsJson(['type' => 'user']);
    }

    public function loginKnownUserJsonApi(ApiTester $I)
    {
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseCodeIs(Response::OK);
        //@TODO: Improve testing, the format will be 'data' => [ [jwt],[session]{,[user]} ]
        $I->seeResponseIsJsonApiSuccessful();
        $I->seeSuccessJsonResponse('data', [
            'type' => 'jwt',
            'type' => 'session',
            'type' => 'user'
        ]);
        $I->seeResponseContainsJson(['type' => 'jwt']);
        $I->seeResponseContainsJson(['type' => 'session']);
        $I->seeResponseContainsJson(['type' => 'user']);
    }

    /**
     * LOGIN USER: Json / JsonApi - Test throttling
     *

    public function loginThrottlingJson(ApiTester $I)
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

    /**
     * Private functions
     */

    /**
     * The headers 'Content-Type' and 'accept' are removed in this function
     * @param ApiTester $I
     * @return string
     */
    private function getCSRFTokenJson(ApiTester $I): string
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET(Data::$loginUrl);
        $I->deleteHeader('Content-Type');
        $I->deleteHeader('accept');
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        return trim($I->grabResponse(), '"');
    }
}
