<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Polls\Tracks\Rates;

use Codeception\Util\HttpCode;
use Discoveryfy\Tests\api\Groups\GroupsPostCest;
use Discoveryfy\Tests\api\Groups\Polls\GroupsPollsPostCest;
use Discoveryfy\Tests\api\Polls\Tracks\PollsTracksPostCest;
use Page\Data;
use Step\Api\Login;

class PollsTracksRatesPutCest
{
    public function modifyRateAsAnonJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJson($I, $groupsPost, $pollsPost);

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [ 'public_votes' => true ]);
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::pollResponseJsonType(), [
            'type'                              => 'polls',
            'attributes.public_votes'           => true,
        ]);

        $this->modifyRateJson($I, $anon_jwt, $poll_uuid, $track_uuid);
        return [ $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid ];
    }

    public function modifyRateAsAnonJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsAnonJsonApi($I, $groupsPost, $pollsPost);

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollUrl, $poll_uuid), [ 'public_votes' => true ]);
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::pollResponseJsonApiType(), [
            'type'                              => 'polls',
            'attributes' => [
                'public_votes'                  => true,
            ]
        ]);

        $this->modifyRateJsonApi($I, $anon_jwt, $poll_uuid, $track_uuid);
        return [ $anon_jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid ];
    }

    public function modifyRateAsTestJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJson($I, $groupsPost, $pollsPost);

        $this->modifyRateJson($I, $jwt, $poll_uuid, $track_uuid);

        return [ $jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid ];
    }

    public function modifyRateAsTestJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $tracksPost->createTrackAsTestJsonApi($I, $groupsPost, $pollsPost);

        $this->modifyRateJsonApi($I, $jwt, $poll_uuid, $track_uuid);

        return [ $jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid ];
    }

    private function modifyRateJson(Login $I, string $jwt, string $poll_uuid, string $track_uuid): void
    {
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid));
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::trackResponseJsonType(), [
            'type' => 'tracks',
        ]);

//        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
//        $I->setContentType('application/json');
//        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
//        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
//        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::rateResponseJsonType(), [
            'type' => 'rates',
            'attributes.rate' => 5,
        ]);
    }

    private function modifyRateJsonApi(Login $I, string $jwt, string $poll_uuid, string $track_uuid): void
    {
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $jwt);
        $I->sendGET(sprintf(Data::$pollTrackUrl, $poll_uuid, $track_uuid));
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::trackResponseJsonApiType(), [
            'type' => 'tracks',
        ]);

//        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
//        $I->setContentType('application/vnd.api+json');
//        $I->haveHttpHeader('Authorization', 'Bearer ' . $anon_jwt);
//        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
//        $I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, 'Only available to registered users');

        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer ' . $jwt);
        $I->sendPUT(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid), [ 'rate' => 5 ]);
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::rateResponseJsonApiType(), [
            'type' => 'rates',
            'attributes' => [
                'rate' => 5,
            ]
        ]);
    }
}
