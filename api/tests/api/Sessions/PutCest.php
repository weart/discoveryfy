<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Sessions;

use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Security\Random;
use Step\Api\Login;

class SessionsPutCest
{
    public function ModifySessionNoNameJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
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

    public function ModifySessionNoNameJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id));
        $title = 'Undefined name';
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $title);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), []);
        $title = 'Undefined name';
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, $title);
    }

    public function ModifySessionEmptyNameJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $empty_name = '';
        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), [
            'name' => $empty_name
        ]);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string:!empty',
            'id'                    => 'string:!empty',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseContainsJson(['attributes.name' => $empty_name]);
    }

    public function modifySessionJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
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
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType([
            'type'                  => 'string:!empty',
            'id'                    => 'string:!empty',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ]);
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $I->seeResponseContainsJson(['attributes.name' => $new_name]);
    }

    public function modifySessionJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
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
            'type'          => 'string:!empty',
            'id'            => 'string:!empty',
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
}
