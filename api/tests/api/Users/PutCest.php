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
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::userResponseJsonType(), [
            'type' => 'users',
            'attributes.username' => $new_name
        ]);
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
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::userResponseJsonApiType(), [
            'type' => 'users',
            'attributes'    => [
                'username'      => $new_name,
                'theme'         => 'default',
                'rol'           => 'ROLE_USER',
            ]
        ]);
    }
}
