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
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $poll_data = Data::pollJson();
        $I->sendPOST(sprintf(Data::$groupPollsUrl, $group_uuid), $poll_data);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseMatchesJsonType([
            'type'                                      => 'string:!empty',
            'id'                                        => 'string:!empty',
            //organization_id
            'attributes.created_at'                     => 'string:date',
            'attributes.updated_at'                     => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'                           => 'string:!empty',
            'attributes.description'                    => 'string',
            'attributes.spotify_playlist_images'        => 'array|string:empty',
            'attributes.spotify_playlist_public'        => 'boolean',
            'attributes.spotify_playlist_collaborative' => 'boolean',
            'attributes.spotify_playlist_uri'           => 'string',
            'attributes.spotify_playlist_winner_uri'    => 'string',
            'attributes.spotify_playlist_historic_uri'  => 'string',
            'attributes.start_date'                     => 'string:date|string',
            'attributes.end_date'                       => 'string:date|string',
            'attributes.restart_date'                   => 'string',
            'attributes.public_visibility'              => 'boolean',
            'attributes.public_votes'                   => 'boolean',
            'attributes.anon_can_vote'                  => 'boolean',
            'attributes.who_can_add_track'              => 'string',
            'attributes.anon_votes_max_rating'          => 'integer', //:>=0 is invalid
            'attributes.user_votes_max_rating'          => 'integer:>0',
            'attributes.multiple_user_tracks'           => 'boolean',
            'attributes.multiple_anon_tracks'           => 'boolean',
            'links.self'                                => 'string:url'
        ]);
        $I->seeResponseContainsJson([
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
        ]);
        $poll_uuid = $I->grabDataFromResponseByJsonPath('$.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid];
    }

    public function createPollJsonApi(Login $I, GroupsPostCest $groupsPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid) = $groupsPost->createGroupAsTestJson($I);

        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt); //Not needed, already setted
        $poll_data = Data::pollJson();
        $I->sendPOST(sprintf(Data::$groupPollsUrl, $group_uuid), $poll_data);

        $I->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseMatchesJsonType([
            'id'                                => 'string:!empty', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                //organization_id
                'created_at'                    => 'string:date', //'2020-03-23 11:57:46'
                'updated_at'                     => 'string:date|string', //When is empty is not null... is an empty string
                'name'                           => 'string:!empty',
                'description'                    => 'string',
                'spotify_playlist_images'        => 'array|string:empty',
                'spotify_playlist_public'        => 'boolean',
                'spotify_playlist_collaborative' => 'boolean',
                'spotify_playlist_uri'           => 'string',
                'spotify_playlist_winner_uri'    => 'string',
                'spotify_playlist_historic_uri'  => 'string',
                'start_date'                     => 'string:date|string',
                'end_date'                       => 'string:date|string',
                'restart_date'                   => 'string',
                'public_visibility'              => 'boolean',
                'public_votes'                   => 'boolean',
                'anon_can_vote'                  => 'boolean',
                'who_can_add_track'              => 'string',
                'anon_votes_max_rating'          => 'integer',
                'user_votes_max_rating'          => 'integer:>0',
                'multiple_user_tracks'           => 'boolean',
                'multiple_anon_tracks'           => 'boolean',
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ], '$.data');
        $I->seeSuccessJsonResponse('data', [
            'type'                          => 'polls',
            'attributes' => [
                'updated_at'             => '',
                'name'                   => $poll_data['name'],
                'description'            => $poll_data['description'],
                'public_visibility'      => $poll_data['public_visibility'],
                'public_votes'           => $poll_data['public_votes'],
                'anon_can_vote'          => $poll_data['anon_can_vote'],
                'who_can_add_track'      => $poll_data['who_can_add_track'],
                'anon_votes_max_rating'  => $poll_data['anon_votes_max_rating'],
                'user_votes_max_rating'  => $poll_data['user_votes_max_rating'],
                'multiple_user_tracks'   => $poll_data['multiple_user_tracks'],
                'multiple_anon_tracks'   => $poll_data['multiple_anon_tracks']
            ]
        ]);
        $poll_uuid = $I->grabDataFromResponseByJsonPath('$.data.id')[0];
        return [$jwt, $session_id, $user_id, $group_uuid, $poll_uuid];
    }
}
