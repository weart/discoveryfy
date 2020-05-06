<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Polls;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class PollsGetCollectionCest
{
    public function anyGetPublicPollsJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(Data::$pollsUrl);
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid Token');
    }

    public function anyGetPublicPollsJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$pollsUrl);
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Invalid Token');
    }

    public function anonGetPublicPollsJson(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$pollsUrl);
        $I->seeCollectionResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonType(),
            [
                'type' => 'polls',
            ]
        );
    }

    public function anonGetPublicPollsJsonApi(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$pollsUrl);
        $I->seeCollectionResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonApiType(),
            [
                'type' => 'polls',
            ]
        );
    }

    public function memberGetPublicOrGroupPollsJson(Login $I)
    {
        list($test_jwt, $test_session_id, $test_user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(Data::$pollsUrl);
        $I->seeCollectionResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonType(),
            [
                'type' => 'polls',
            ]
        );
    }

    public function memberGetPublicOrGroupPollsJsonApi(Login $I)
    {
        list($test_jwt, $test_session_id, $test_user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(Data::$pollsUrl);
        $I->seeCollectionResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonApiType(),
            [
                'type' => 'polls',
            ]
        );
    }
}
