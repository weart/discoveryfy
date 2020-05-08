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

class PollsTracksPutCest
{
    public function modifyTrackAsAnonJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJson($I, $groupsPost, $pollsPost);
        $this->modifyTrackJson($I, $anon_jwt, $poll_uuid, $track_uuid);
    }

    public function modifyTrackAsAnonJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJsonApi($I, $groupsPost, $pollsPost);
        $this->modifyTrackJsonApi($I, $anon_jwt, $poll_uuid, $track_uuid);
    }

    public function modifyTrackAsTestJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJson($I, $groupsPost, $pollsPost);
        $this->modifyTrackJson($I, $jwt, $poll_uuid, $track_uuid);
    }

    public function modifyTrackAsTestJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJsonApi($I, $groupsPost, $pollsPost);
        $this->modifyTrackJsonApi($I, $jwt, $poll_uuid, $track_uuid);
    }

    private function modifyTrackJson(Login $I, string $jwt, string $poll_uuid, string $track_uuid): void
    {
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid));
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonType(),
            [
                'type'                      => 'tracks',
            ]
        );

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendPUT(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid), [
            'artist' => 'The Skatalites',
            'name' => 'Requiem for Rico',
            'spotify_uri' => '7aSRHl639kQIa81lTDG7BD',
            'youtube_uri' => '',
        ]);
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only admins and owners can modify a track');

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid), [
            'artist' => 'The Skatalites',
            'name' => 'Requiem for Rico',
            'spotify_uri' => '7aSRHl639kQIa81lTDG7BD',
            'youtube_uri' => '',
        ]);
        $I->seeItemResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonType(),
            [
                'type'                      => 'tracks',
                'attributes.artist'         => 'The Skatalites',
                'attributes.name'           => 'Requiem for Rico',
                'attributes.spotify_uri'    => '7aSRHl639kQIa81lTDG7BD',
                'attributes.youtube_uri'    => '',
            ]
        );
    }

    private function modifyTrackJsonApi(Login $I, string $jwt, string $poll_uuid, string $track_uuid): void
    {
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid));
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonApiType(),
            [
                'type'                      => 'tracks',
            ]
        );

        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendPUT(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid), [
            'artist' => 'The Skatalites',
            'name' => 'Requiem for Rico',
            'spotify_uri' => '7aSRHl639kQIa81lTDG7BD',
            'youtube_uri' => '',
        ]);
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only admins and owners can modify a track');

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid), [
            'artist' => 'The Skatalites',
            'name' => 'Requiem for Rico',
            'spotify_uri' => '7aSRHl639kQIa81lTDG7BD',
            'youtube_uri' => '',
        ]);
        $I->seeItemResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonApiType(),
            [
                'type'                      => 'tracks',
                'attributes' => [
                    'artist'                => 'The Skatalites',
                    'name'                  => 'Requiem for Rico',
                    'spotify_uri'           => '7aSRHl639kQIa81lTDG7BD',
                    'youtube_uri'           => ''
                ]
            ]
        );
    }
}
