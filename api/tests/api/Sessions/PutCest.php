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
    public function modifySessionNoNameJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id));
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Undefined name');

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), []);
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Undefined name');
    }

    public function modifySessionNoNameJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id));
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Undefined name');

        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), []);
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Undefined name');
    }

    public function modifySessionEmptyNameJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $empty_name = '';
        $I->sendPUT(sprintf(Data::$sessionsUrl, $session_id), [
            'name' => $empty_name
        ]);

        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::sessionResponseJsonType(), [
            'type' => 'sessions',
            'attributes.name' => $empty_name
        ]);
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

        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::sessionResponseJsonType(), [
            'type' => 'sessions',
            'attributes.name' => $new_name
        ]);
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

        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::sessionResponseJsonApiType(), [
            'type'          => 'sessions',
            'attributes'    => [
                'name'      => $new_name
            ]
        ]);
    }
}
