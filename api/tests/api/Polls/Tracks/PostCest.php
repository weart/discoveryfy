<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Polls\Tracks;

use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Discoveryfy\Tests\api\Groups\Polls\GroupsPollsPostCest;
use Page\Data;
use Step\Api\Login;

class PollsTracksPostCest
{
    public function createTrackAsTestJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);

        $I->seeItemResponseIsJsonSuccessful(HttpCode::CREATED, Data::trackResponseJsonType(), [
            'type'                      => 'tracks',
            'attributes.updated_at'     => '',
            'attributes.artist'         => $this->encodeSpecialChars($track_data['artist']),
            'attributes.name'           => $this->encodeSpecialChars($track_data['name']),
            'attributes.spotify_uri'    => $track_data['spotify_uri'] ?? '',
            'attributes.youtube_uri'    => $track_data['youtube_uri'] ?? '',
        ]);
        $track_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid];
    }

    public function createTrackAsTestJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);

        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::CREATED, Data::trackResponseJsonApiType(), [
            'type'                      => 'tracks',
            'attributes' => [
                'updated_at'            => '',
                'artist'                => $this->encodeSpecialChars($track_data['artist']),
                'name'                  => $this->encodeSpecialChars($track_data['name']),
                'spotify_uri'           => $track_data['spotify_uri'] ?? '',
                'youtube_uri'           => $track_data['youtube_uri'] ?? '',
            ]
        ]);
        $track_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid];
    }

    public function createTrackAsAnonJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
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

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->comment(sprintf('Changing who_can_add_track from: %s, to: %s', 'OWNERS', 'ANYONE'));
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [
            'public_visibility' => true,
            'who_can_add_track' => 'ANYONE'
        ]);
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::pollResponseJsonType(), [
            'type'                          => 'polls',
            'attributes.public_visibility'  => true,
            'attributes.who_can_add_track'  => 'ANYONE',
        ]);

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);
        $I->seeItemResponseIsJsonSuccessful(HttpCode::CREATED, Data::trackResponseJsonType(), [
            'type'                      => 'tracks',
            'attributes.updated_at'     => '',
            'attributes.artist'         => $this->encodeSpecialChars($track_data['artist']),
            'attributes.name'           => $this->encodeSpecialChars($track_data['name']),
            'attributes.spotify_uri'    => $track_data['spotify_uri'] ?? '',
            'attributes.youtube_uri'    => $track_data['youtube_uri'] ?? '',
        ]);
        $track_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $anon_jwt, $anon_session_id, $anon_user_id, $group_uuid, $poll_uuid, $track_uuid];
    }

    public function createTrackAsAnonJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
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

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->comment(sprintf('Changing who_can_add_track from: %s, to: %s', 'OWNERS', 'ANYONE'));
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [
            'public_visibility' => true,
            'who_can_add_track' => 'ANYONE'
        ]);
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::pollResponseJsonApiType(), [
            'type'                      => 'polls',
            'attributes' => [
                'public_visibility'     => true,
                'who_can_add_track'     => 'ANYONE',
            ]
        ]);

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $track_data = Data::trackJson();
        $I->sendPOST(sprintf(Data::$pollTracksUrl, $poll_uuid), $track_data);
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::CREATED, Data::trackResponseJsonApiType(), [
            'type'                      => 'tracks',
            'attributes' => [
                'updated_at'            => '',
                'artist'                => $this->encodeSpecialChars($track_data['artist']),
                'name'                  => $this->encodeSpecialChars($track_data['name']),
                'spotify_uri'           => $track_data['spotify_uri'] ?? '',
                'youtube_uri'           => $track_data['youtube_uri'] ?? ''
            ]
        ]);
        $track_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $anon_jwt, $anon_session_id, $anon_user_id, $group_uuid, $poll_uuid, $track_uuid];
    }

//    public function createTrackAsUserJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
//    {
//        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJson($I, $groupsPost);
//        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
//        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));
//        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::pollResponseJsonType(), [
//            'type'                              => 'polls',
//            // From Data::pollJson()
//            'attributes.public_visibility'      => false,
//            'attributes.public_votes'           => false,
//            'attributes.anon_can_vote'          => false,
//            'attributes.who_can_add_track'      => 'OWNERS',
//            'attributes.anon_votes_max_rating'  => 0,
//            'attributes.user_votes_max_rating'  => 1,
//            'attributes.multiple_user_tracks'   => true,
//            'attributes.multiple_anon_tracks'   => false,
//        ]);
//
//        list($admin_jwt, $admin_session_id, $admin_user_id) = $I->loginAsAdmin();
//        // Should login as a 3rd user not belong to the group and check the $poll->get('who_can_add_track') !== 'USERS' setting
//    }

//    public function createTrackAsUserJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost)
//    {
//        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid) = $pollsPost->createPollJsonApi($I, $groupsPost);
//        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
//        $I->sendGET(sprintf(Data::$pollUrl, $poll_uuid));
//        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::pollResponseJsonApiType(), [
//            'type'                      => 'polls',
//            'attributes' => [
//                // From Data::pollJson()
//                'public_visibility'     => false,
//                'public_votes'          => false,
//                'anon_can_vote'         => false,
//                'who_can_add_track'     => 'OWNERS',
//                'anon_votes_max_rating' => 0,
//                'user_votes_max_rating' => 1,
//                'multiple_user_tracks'  => true,
//                'multiple_anon_tracks'  => false,
//            ]
//        ]);
//
//        list($admin_jwt, $admin_session_id, $admin_user_id) = $I->loginAsAdmin();
//        // Should login as a 3rd user not belong to the group and check the $poll->get('who_can_add_track') !== 'USERS' setting
//    }

    private function encodeSpecialChars(string $str)
    {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
    }
}
