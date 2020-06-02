<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Phalcon\Api\Tasks\BaseTask;
use const PHP_EOL;

class RestartPollsTask extends BaseTask
{
    public function helpAction()
    {
        echo $this->formatTitle('Restart Polls');
        echo 'Check if any poll should be restarted'.PHP_EOL;
        parent::helpAction();
    }

    public function runAction()
    {
        echo $this->formatTitle('Checking polls should be restarted');
        $this->format('@ToDo', 'red');
        //RestartPollUpdateSpotifyPlaylistHistoricTracksWorker
        //RestartPollUpdateSpotifyPlaylistWinnerTracksWorker
    }
}
