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

        $I->seeResponseIsValidJson(
            HttpCode::OK,
            [
                'type'                  => 'string:!empty',
                'id'                    => 'string:!empty',
                'attributes.created_at' => 'string:date',
                'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
                'attributes.name'       => 'string',
                'links.self'            => 'string:url',
            ],
            [
                'type' => 'sessions',
                'attributes.name' => $empty_name
            ]
        );
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

        $I->seeResponseIsValidJson(
            HttpCode::OK,
            [
                'type'                  => 'string:!empty',
                'id'                    => 'string:!empty',
                'attributes.created_at' => 'string:date',
                'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
                'attributes.name'       => 'string',
                'links.self'            => 'string:url',
            ],
            [
                'type' => 'sessions',
                'attributes.name' => $new_name
            ]
        );
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

        $I->seeResponseIsValidJsonApi(
            HttpCode::OK,
            [
                'type'          => 'string:!empty',
                'id'            => 'string:!empty',
                'attributes'    => [
                    'created_at'    => 'string:date',
                    'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
                    'name'          => 'string',
                ],
                'links'         => [
                    'self'      => 'string:url',
                ],
            ],
            [
                'type'          => 'sessions',
                'attributes'    => [
                    'name'      => $new_name
                ]
            ]
        );
    }
}
