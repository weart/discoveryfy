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

class GroupsGetItemCest
{
    private $unauthorized_msg = 'Only available when the group has public_visibility or you belong to the group';

    public function anonGetPublicGroupJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($test_jwt, $test_session_id, $test_user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();

        // By default public visibility is false, anon can't see the group
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        // Allow public visibility
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendPUT(sprintf(Data::$groupUrl, $group_uuid), [ 'public_visibility' => true ]);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonType(),
            [
                'type'                          => 'groups',
                'attributes.public_visibility'  => true,
            ]
        );

        // Anon should be able to see the group
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonType(),
            [
                'type'                          => 'groups',
                'attributes.public_visibility'  => true,
            ]
        );

        // Delete the group
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsValidDeleteJson();

        // Nobody can see the group
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }

    public function anonGetPublicGroupJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($test_jwt, $test_session_id, $test_user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();

        // By default public visibility is false, anon can't see the group
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        // Allow public visibility
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendPUT(sprintf(Data::$groupUrl, $group_uuid), [ 'public_visibility' => true ]);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonApiType(),
            [
                'type'                  => 'groups',
                'attributes' => [
                    'public_visibility' => true,
                ]
            ]
        );

        // Anon should be able to see the group
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::groupResponseJsonApiType(),
            [
                'type'                  => 'groups',
                'attributes' => [
                    'public_visibility' => true,
                ]
            ]
        );

        // Delete the group
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendDELETE(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsValidDeleteJsonApi();

        // Nobody can see the group
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$test_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $group_uuid));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }
}
