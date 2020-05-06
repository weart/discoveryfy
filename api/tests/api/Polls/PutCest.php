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

class PollsPutCest
{
    public function modifyPollJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeItemResponseIsJsonSuccessful(
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

        $prev_name = $I->grabDataFromResponseByJsonPath('$["attributes.name"]')[0];
        $prev_description = $I->grabDataFromResponseByJsonPath('$["attributes.description"]')[0];
        $new_name = 'test_'.(new Random())->hex(5);
        $new_description = 'test_'.(new Random())->hex(5);
        if (empty($prev_name) || empty($prev_description)) {
            throw new TestRuntimeException('Error in JsonPath');
        }
        if ($prev_name === $new_name || $prev_description === $new_description) {
            throw new TestRuntimeException('Very strange error and very unlikely');
        }

        $I->comment(sprintf('Changing from name: %s, desc: %s to %s, %s', $prev_name, $prev_description, $new_name, $new_description));
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [
            'name' => $new_name,
            'description' => $new_description
        ]);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonType(),
            [
                'type'                              => 'polls',
                'attributes.name'                   => $new_name,
                'attributes.description'            => $new_description,
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
    }

    public function modifyPollJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJsonApi($I, $groupsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));

        $I->seeItemResponseIsJsonApiSuccessful(
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

        $prev_name = $I->grabDataFromResponseByJsonPath('$.data.attributes.name')[0];
        $prev_description = $I->grabDataFromResponseByJsonPath('$.data.attributes.description')[0];
        $new_name = 'test_'.(new Random())->hex(5);
        $new_description = 'test_'.(new Random())->hex(5);
        if (empty($prev_name) || empty($prev_description)) {
            throw new TestRuntimeException('Error in JsonPath');
        }
        if ($prev_name === $new_name || $prev_description === $new_description) {
            throw new TestRuntimeException('Very strange error and very unlikely');
        }

        $I->comment(sprintf('Changing from name: %s, desc: %s to %s, %s', $prev_name, $prev_description, $new_name, $new_description));
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [
            'name' => $new_name,
            'description' => $new_description
        ]);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::pollResponseJsonApiType(),
            [
                'type'                      => 'polls',
                'attributes' => [
                    'name'                   => $new_name,
                    'description'            => $new_description,
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
    }
}
