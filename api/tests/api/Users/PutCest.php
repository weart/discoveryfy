<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Users;

use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Security\Random;
use Step\Api\Login;

class UsersPutCest
{
    public function modifyUserJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $session_id));
        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.username"]');

        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);

//        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$usersUrl, $user_id), [
            'username' => $new_name
        ]);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            [
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
            ],
            [
                'type' => 'users',
                'attributes.username' => $new_name
            ]
        );
    }

    public function modifyUserJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$usersUrl, $user_id));

        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.username"]');
        $new_name = 'test_'.(new Random())->hex(5);
        $I->assertNotEquals($prev_name, $new_name);

//        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$usersUrl, $user_id), [
            'username' => $new_name
        ]);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            [
                'type'              => 'string',
                'id'                => 'string',
                'attributes'    => [
                    'created_at'    => 'string:date',
                    'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
                    'username'      => 'string',
                    'email'         => 'string:email',
                    'language'      => 'string',
                    'theme'         => 'string',
                    'rol'           => 'string',
                ],
                'links'         => [
                    'self'          => 'string:url',
                ],
            ],
            [
                'type' => 'users',
                'attributes'    => [
                    'username'      => $new_name,
                    'theme'         => 'default',
                    'rol'           => 'ROLE_USER',
                ]
            ]
        );
    }
}
