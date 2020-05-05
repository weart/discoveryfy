<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups\Polls;

use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Page\Data;
use Step\Api\Login;

class GroupsPollsGetCollectionCest
{
    private $unauthorized_msg = 'Only available to registered users';

    public function memberGetGroupPollsJson(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $groupsPollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $groupsPollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->comment($jwt);
        $I->sendGET(sprintf(Data::$groupPollsUrl, $group_uuid));
        $I->seeCollectionResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonType(),
            [
                'type'                              => 'polls',
                // From Data::pollJson()
                'attributes.public_visibility'      => false,
                'attributes.public_votes'           => false,
                'attributes.anon_can_vote'          => false,
                'attributes.who_can_add_track'      => 'OWNERS',
                'attributes.anon_votes_max_rating'  => 0,
                'attributes.user_votes_max_rating'  => 1,
                'attributes.multiple_user_tracks'   => true,
                'attributes.multiple_anon_tracks'   => false,
            ]
        );

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupPollsUrl, $group_uuid));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }

    public function memberGetGroupPollsJsonApi(Login $I, GroupsPostCest $groupsPost, GroupsPollsPostCest $groupsPollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $groupsPollsPost->createPollJsonApi($I, $groupsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->comment($jwt);
        $I->sendGET(sprintf(Data::$groupPollsUrl, $group_uuid));
        $I->seeCollectionResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonApiType(),
            [
                'type'                      => 'polls',
                'attributes' => [
                    // From Data::pollJson()
                    'public_visibility'     => false,
                    'public_votes'          => false,
                    'anon_can_vote'         => false,
                    'who_can_add_track'     => 'OWNERS',
                    'anon_votes_max_rating' => 0,
                    'user_votes_max_rating' => 1,
                    'multiple_user_tracks'  => true,
                    'multiple_anon_tracks'  => false,
                ]
            ]
        );

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupPollsUrl, $group_uuid));
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);
    }
}
