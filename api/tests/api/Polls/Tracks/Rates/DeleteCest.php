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

class PollsTracksRatesDeleteCest
{
    public function deleteRateAsAnonJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost, PollsTracksRatesPutCest $ratesPut)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $ratesPut->modifyRateAsAnonJson($I, $groupsPost, $pollsPost, $tracksPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendDELETE(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid));
        $I->seeResponseIsValidDeleteJson();
    }

    public function deleteRateAsAnonJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost, PollsTracksRatesPutCest $ratesPut)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $ratesPut->modifyRateAsAnonJsonApi($I, $groupsPost, $pollsPost, $tracksPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendDELETE(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid));
        $I->seeResponseIsValidDeleteJsonApi();
    }

    public function deleteRateAsTestJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost, PollsTracksRatesPutCest $ratesPut)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $ratesPut->modifyRateAsTestJson($I, $groupsPost, $pollsPost, $tracksPost);
        $I->setContentType('application/json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendDELETE(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid));
        $I->seeResponseIsValidDeleteJson();
    }

    public function deleteRateAsTestJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost, PollsTracksRatesPutCest $ratesPut)
    {
        list($jwt, $session_id, $user_id, $group_uuid, $poll_uuid, $track_uuid) = $ratesPut->modifyRateAsTestJsonApi($I, $groupsPost, $pollsPost, $tracksPost);
        $I->setContentType('application/vnd.api+json');
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendDELETE(sprintf(Data::$pollTrackRateUrl, $poll_uuid, $track_uuid));
        $I->seeResponseIsValidDeleteJsonApi();
    }
}
