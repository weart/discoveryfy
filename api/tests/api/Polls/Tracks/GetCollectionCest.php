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
use Page\Data;
use Step\Api\Login;

class PollsTracksGetCollectionCest
{
    public function anyGetTracksJson(Login $I) {}
    public function anyGetTracksJsonApi(Login $I) {}
    public function anonGetTracksJson(Login $I) {}
    public function anonGetTracksJsonApi(Login $I) {}
    public function memberGetTracksJson(Login $I) {}
    public function memberGetTracksJsonApi(Login $I) {}
}
