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

class PollsGetItemCest
{
    public function getPollJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::pollResponseJsonType(), [
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
        ]);
    }

    public function getPollJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJsonApi($I, $groupsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::pollResponseJsonApiType(), [
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
        ]);
    }
}
