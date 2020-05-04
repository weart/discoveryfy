<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class GroupsPostCest
{
    public function createGroupAsTestJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $group_data = Data::groupJson();
        $I->sendPOST(Data::$groupsUrl, $group_data);

        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::CREATED,
            Data::groupResponseJsonType(),
            [
                'type'                              => 'groups',
                'attributes.updated_at'             => '',
                'attributes.name'                   => $group_data['name'],
                'attributes.description'            => $group_data['description'],
                'attributes.public_visibility'      => $group_data['public_visibility'],
                'attributes.public_membership'      => $group_data['public_membership'],
                'attributes.who_can_create_polls'   => $group_data['who_can_create_polls']
            ]
        );
        $group_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid];
    }

    public function createGroupAsTestJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $group_data = Data::groupJson();
        $I->sendPOST(Data::$groupsUrl, $group_data);

        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::CREATED,
            Data::groupResponseJsonApiType(),
            [
                'type'                          => 'groups',
                'attributes' => [
                    'name'                      => $group_data['name'],
                    'description'               => $group_data['description'],
                    'public_visibility'         => $group_data['public_visibility'],
                    'public_membership'         => $group_data['public_membership'],
                    'who_can_create_polls'      => $group_data['who_can_create_polls'],
                    'updated_at'                => '',
                ]
            ]
        );
        $group_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid];
    }
}
