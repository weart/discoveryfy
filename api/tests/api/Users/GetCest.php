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

class UsersGetCest
{
    public function getUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));

        $I->seeResponseContainsNoErrors();
        $I->seeResponseIsValidJson(
            HttpCode::OK,
            [
                'type'                  => 'string:!empty',
                'id'                    => 'string:!empty',
                'attributes.created_at' => 'string:date',
                'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
                'attributes.username'   => 'string:!empty',
                'attributes.email'      => 'string:email',
                'attributes.language'   => 'string:!empty',
                'attributes.theme'      => 'string:!empty',
                'attributes.rol'        => 'string:!empty',
                'links.self'            => 'string:url',
            ],
            [
                'type' => 'users'
            ]
        );
    }

    public function getUserJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));

        $I->seeResponseContainsNoErrors();
        $I->seeResponseIsValidJsonApi(
            HttpCode::OK,
            [
                'type'          => 'string:!empty',
                'id'            => 'string:!empty',
                'attributes'    => [
                    'created_at'    => 'string:date',
                    'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
                    'username'      => 'string:!empty',
                    'email'         => 'string:email',
                    'language'      => 'string:!empty',
                    'theme'         => 'string:!empty',
                    'rol'           => 'string:!empty',
                ],
                'links'         => [
                    'self'          => 'string:url',
                ],
            ],
            [
                'type'          => 'users',
                'attributes'    => [
                    'theme'         => 'default',
                    'rol'           => 'ROLE_USER',
                ],
            ]
        );
    }

    public function getInvalidUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, '1234'));

        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid uuid');
    }

    public function getInvalidUserJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, '1234'));

        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Invalid uuid');
    }

    public function getUnauthorizedUserJson(Login $I)
    {
        list($admin_jwt, $admin_session_id, $admin_user_id) = $I->loginAsAdmin();
        list($test_jwt, $test_session_id, $test_user_id) = $I->loginAsTest();


        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $admin_user_id));

        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'User unauthorized for this action');
    }

    public function getUnauthorizedUserJsonApi(Login $I)
    {
        list($admin_jwt, $admin_session_id, $admin_user_id) = $I->loginAsAdmin();
        list($test_jwt, $test_session_id, $test_user_id) = $I->loginAsTest();


        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $admin_user_id));

        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'User unauthorized for this action');
    }
}
