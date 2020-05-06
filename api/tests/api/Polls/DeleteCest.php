<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Polls;

use Codeception\Exception\TestRuntimeException;
use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Discoveryfy\Tests\api\Groups\Polls\GroupsPollsPostCest;
use Page\Data;
use Phalcon\Security\Random;
use Step\Api\Login;

class PollsDeleteCest
{
    public function deletePollJson(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $jwt);
        $I->sendDELETE(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeResponseIsValidDeleteJson();
    }

    public function deletePollJsonApi(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJsonApi($I, $groupsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $jwt);
        $I->sendDELETE(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeResponseIsValidDeleteJsonApi();
    }

    public function anonCantDeleteGroupJson(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $anon_jwt);
        $I->sendDELETE(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only available to registered users');
    }

    public function anonCantDeleteGroupJsonApi(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJsonApi($I, $groupsPost);
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $anon_jwt);
        $I->sendDELETE(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only available to registered users');
    }
}
