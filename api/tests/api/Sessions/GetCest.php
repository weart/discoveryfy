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
use Step\Api\Login;

class SessionsGetCest
{
    public function getSessionJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

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
    }

    public function getSessionJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$sessionsUrl, $session_id));

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
            'name'       => 'string',
        ], '$.data.attributes');
        $I->seeResponseMatchesJsonType([
            'self'          => 'string:url',
        ], '$.data.links');
    }
}
