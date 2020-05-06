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
    public function modifyTrackJson(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        //
    }

    public function modifyTrackJsonApi(Login $I, GroupsPostCest $groupsPost,  GroupsPollsPostCest $pollsPost, PollsTracksPostCest $tracksPost)
    {
        //
    }
}
