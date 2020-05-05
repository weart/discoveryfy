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

class GroupsPollsPostCest
{
    public function createPollJson(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $poll_data = Data::pollJson();
        $I->sendPOST(sprintf(Data::$groupPollsUrl, $group_uuid), $poll_data);

        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::CREATED,
            Data::pollResponseJsonType(),
            [
                'type'                              => 'polls',
                'attributes.updated_at'             => '',
                'attributes.name'                   => $poll_data['name'],
                'attributes.description'            => $poll_data['description'],
                'attributes.public_visibility'      => $poll_data['public_visibility'],
                'attributes.public_votes'           => $poll_data['public_votes'],
                'attributes.anon_can_vote'          => $poll_data['anon_can_vote'],
                'attributes.who_can_add_track'      => $poll_data['who_can_add_track'],
                'attributes.anon_votes_max_rating'  => $poll_data['anon_votes_max_rating'],
                'attributes.user_votes_max_rating'  => $poll_data['user_votes_max_rating'],
                'attributes.multiple_user_tracks'   => $poll_data['multiple_user_tracks'],
                'attributes.multiple_anon_tracks'   => $poll_data['multiple_anon_tracks']
            ]
        );
        $poll_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid];
    }

    public function createPollJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $poll_data = Data::pollJson();
        $I->sendPOST(sprintf(Data::$groupPollsUrl, $group_uuid), $poll_data);

        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::CREATED,
            Data::pollResponseJsonApiType(),
            [
                'type'                               => 'polls',
                'attributes' => [
                    'updated_at'                     => '',
                    'name'                           => $poll_data['name'],
                    'description'                    => $poll_data['description'],
                    'public_visibility'              => $poll_data['public_visibility'],
                    'public_votes'                   => $poll_data['public_votes'],
                    'anon_can_vote'                  => $poll_data['anon_can_vote'],
                    'who_can_add_track'              => $poll_data['who_can_add_track'],
                    'anon_votes_max_rating'          => $poll_data['anon_votes_max_rating'],
                    'user_votes_max_rating'          => $poll_data['user_votes_max_rating'],
                    'multiple_user_tracks'           => $poll_data['multiple_user_tracks'],
                    'multiple_anon_tracks'           => $poll_data['multiple_anon_tracks']
                ]
            ]
        );
        $poll_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid];
    }
}
