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

class GroupsGetCollectionCest
{
    public function anonGetPublicGroupsJson(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$groupsUrl);
        $I->seeCollectionResponseIsJsonSuccessful(HttpCode::OK, Data::groupResponseJsonType(), [
            'type'                          => 'groups',
            'attributes.public_visibility'  => true,
        ]);
    }

    public function anonGetPublicGroupsJsonApi(Login $I)
    {
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(Data::$groupsUrl);
        $I->seeCollectionResponseIsJsonApiSuccessful(HttpCode::OK, Data::groupResponseJsonApiType(), [
            'type'                  => 'groups',
            'attributes'            =>
            [
                'public_visibility' => true,

            ]
        ]);
    }
}
