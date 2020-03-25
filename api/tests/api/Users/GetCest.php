<?php

namespace Discoveryfy\Tests\api\Sessions;

use ApiTester;
use Page\Data;
use Phalcon\Api\Http\Response;
//use function json_decode;
use Codeception\Exception\TestRuntimeException;

class UsersGetCest
{
    public function getSessionJson(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));
        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string',
            'id'                    => 'string',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.username'   => 'string',
            'attributes.email'      => 'string:email',
            'attributes.language'   => 'string',
            'attributes.theme'      => 'string',
            'attributes.rol'        => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'users']);
    }

    public function getSessionJsonApi(ApiTester $I)
    {
        list($jwt, $session_id, $user_id) = $this->getAuthTokenJson($I);
//        $I->comment(var_dump($session_id, $user_id, $jwt));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));
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
        $I->seeResponseContainsJson(['type' => 'users']);
        $I->seeResponseMatchesJsonType([
            'created_at'    => 'string:date',
            'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
            'username'      => 'string',
            'email'         => 'string:email',
            'language'      => 'string',
            'theme'         => 'string',
            'rol'           => 'string',
        ], '$.data.attributes');
        $I->seeResponseContainsJson(['theme' => 'default']);
        $I->seeResponseContainsJson(['rol' => 'ROLE_USER']);
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
            } else if ($data['type'] === 'user') {
                $user_id = $data['id'];
            } else if ($data['type'] === 'session') {
                $session_id = $data['id'];
            }
        }
        if (empty($jwt) || empty($session_id) || empty($user_id)) {
            throw new TestRuntimeException('Invalid login');
        }
        return [$jwt, $session_id, $user_id];
    }
}
