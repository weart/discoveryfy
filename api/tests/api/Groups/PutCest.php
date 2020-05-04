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
use Phalcon\Security\Random;
use Step\Api\Login;

class GroupsPutCest
{
    public function modifyGroupJson(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['name'] = 'test_'.(new Random())->hex(5);
        $modified_attrs['description'] = 'test_'.(new Random())->hex(5);

        // Change group name and description
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'name' => $modified_attrs['name'],
            'description' => $modified_attrs['description']
        ]);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonType(),
            [
                'type'                              => 'groups',
                'attributes.name'                   => $modified_attrs['name'],
                'attributes.description'            => $modified_attrs['description'],
                'attributes.public_visibility'      => $modified_attrs['public_visibility'],
                'attributes.public_membership'      => $modified_attrs['public_membership'],
                'attributes.who_can_create_polls'   => $modified_attrs['who_can_create_polls']
            ]
        );

        //Leave group as before
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), $previous_attrs);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonType(),
            [
                'type'                              => 'groups',
                'attributes.name'                   => $previous_attrs['name'],
                'attributes.description'            => $previous_attrs['description'],
                'attributes.public_visibility'      => $previous_attrs['public_visibility'],
                'attributes.public_membership'      => $previous_attrs['public_membership'],
                'attributes.who_can_create_polls'   => $previous_attrs['who_can_create_polls']
            ]
        );
    }

    public function modifyGroupJsonApi(Login $I)
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['name'] = 'test_'.(new Random())->hex(5);
        $modified_attrs['description'] = 'test_'.(new Random())->hex(5);

        // Change group name and description
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'name' => $modified_attrs['name'],
            'description' => $modified_attrs['description']
        ]);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonApiType(),
            [
                'type'                          => 'groups',
                'attributes' => [
                    'name'                      => $modified_attrs['name'],
                    'description'               => $modified_attrs['description'],
                    'public_visibility'         => $modified_attrs['public_visibility'],
                    'public_membership'         => $modified_attrs['public_membership'],
                    'who_can_create_polls'      => $modified_attrs['who_can_create_polls'],
                ]
            ]
        );

        //Leave group as before
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), $previous_attrs);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonApiType(),
            [
                'type'                          => 'groups',
                'attributes' => [
                    'name'                      => $previous_attrs['name'],
                    'description'               => $previous_attrs['description'],
                    'public_visibility'         => $previous_attrs['public_visibility'],
                    'public_membership'         => $previous_attrs['public_membership'],
                    'who_can_create_polls'      => $previous_attrs['who_can_create_polls'],
                ]
            ]
        );
    }
}
