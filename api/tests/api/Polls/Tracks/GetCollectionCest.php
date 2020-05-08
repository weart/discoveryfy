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
use Phalcon\Security\Random;
use Step\Api\Login;

class PollsTracksGetCollectionCest
{
    public function anyGetTracksJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(sprintf(Data::$pollTracksUrl, (new Random())->uuid()));
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid Token');
    }

    public function anyGetTracksJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(sprintf(Data::$pollTracksUrl, (new Random())->uuid()));
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Invalid Token');
    }

    public function anonGetTracksJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJson($I, $groupsPost, $pollsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$pollTracksUrl, $poll_uuid));
        $I->seeCollectionResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonType(),
            [
                'type' => 'tracks',
            ]
        );
    }

    public function anonGetTracksJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJsonApi($I, $groupsPost, $pollsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$pollTracksUrl, $poll_uuid));
        $I->seeCollectionResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonApiType(),
            [
                'type' => 'tracks',
            ]
        );
    }

    public function memberGetTracksJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJson($I, $groupsPost, $pollsPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollTracksUrl, $poll_uuid));
        $I->seeCollectionResponseIsJsonSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonType(),
            [
                'type' => 'tracks',
            ]
        );
    }

    public function memberGetTracksJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJsonApi($I, $groupsPost, $pollsPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollTracksUrl, $poll_uuid));
        $I->seeCollectionResponseIsJsonApiSuccessful(
            HttpCode::OK,
            Data::trackResponseJsonApiType(),
            [
                'type' => 'tracks',
            ]
        );
    }
}
