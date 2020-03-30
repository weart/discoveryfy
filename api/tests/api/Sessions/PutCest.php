<?php

namespace Discoveryfy\Tests\api\Sessions;

use ApiTester;
use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Api\Http\Response;
//use function json_decode;
use Codeception\Exception\TestRuntimeException;
use Phalcon\Security\Random;

class SessionsPutCest
{
    public function ModifySessionNoNameJson(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id));
        $I->seeResponseIsJsonSuccessful(HttpCode::BAD_REQUEST);
        $I->seeSuccessJsonResponse('errors', [
            [
                'code' => HttpCode::BAD_REQUEST,
                'status' => HttpCode::BAD_REQUEST,
                'title' => 'Undefined name'
            ]
        ]);
    }

    public function ModifySessionNoNameJsonApi(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id));
        $title = 'Undefined name';
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $title);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), []);
        $title = 'Undefined name';
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $title);
    }

    public function ModifySessionEmptyNameJson(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $empty_name = '';
        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), [
            'name' => $empty_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string',
            'id'                    => 'string',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseContainsJson(['attributes.name' => $empty_name]);
    }

    public function modifySessionJson(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.name"]');
        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);
        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), [
            'name' => $new_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string',
            'id'                    => 'string',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseContainsJson(['attributes.name' => $new_name]);
    }

    public function modifySessionJsonApi(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.name"]');
        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);
        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), [
            'name' => $new_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'        => 'error'
        ]);
        $I->seeResponseIsJsonApiSuccessful();
        $I->seeResponseMatchesJsonType([
            'type'          => 'string',
            'id'            => 'string',
            'attributes'    => 'array',
            'links'         => 'array',
        ], '$.data');
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseMatchesJsonType([
            'created_at'    => 'string:date',
            'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
            'name'          => 'string',
        ], '$.data.attributes');
        $I->seeResponseContainsJson(['name' => $new_name]);
        $I->seeResponseMatchesJsonType([
            'self'          => 'string:url',
        ], '$.data.links');
    }


    /**
     * Private functions, move to a shared place in all tests, but helper seems to fail
     */


    /**
     * The headers 'Content-Type' and 'accept' are removed in this function
     * @return string
     */
    public function getCSRFTokenJson(ApiTester $I): string
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

    public function getAuthTokenJson(ApiTester $I): array
    {
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendPOST(Data::$loginUrl, Data::loginJson());
        $I->seeResponseCodeIs(Response::OK);
        $obj = json_decode($I->grabResponse(), true);
        $jwt = $session_id = $user_id = null;
        foreach ($obj as $data) {
            if ($data['type'] === 'jwt') {
                $jwt = $data['id'];
            } else if ($data['type'] === 'users') {
                $user_id = $data['id'];
            } else if ($data['type'] === 'sessions') {
                $session_id = $data['id'];
            }
        }
        if (empty($jwt) || empty($session_id) || empty($user_id)) {
            throw new TestRuntimeException('Invalid login');
        }
        return [$jwt, $session_id, $user_id];
    }
}
