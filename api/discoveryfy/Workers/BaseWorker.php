<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Workers;

use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Monolog\Logger;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Workers\BaseWorker as BaseApiWorker;
use Phalcon\Db\Column;

abstract class BaseWorker extends BaseApiWorker
{
    protected function getPollById(string $poll_uuid): ?Polls
    {
        if (!($poll = Polls::findFirst([
            'conditions' => 'id = :id:',
            'bind' => [ 'id' => $poll_uuid ],
            'bindTypes' => [ 'id' => Column::BIND_PARAM_STR ],
        ]))) {
            return null;
        }
        return $poll;
    }

    protected function getTrackById(string $track_uuid): ?Tracks
    {
        if (!($track = Tracks::findFirst([
            'conditions' => 'id = :id:',
            'bind' => [ 'id' => $track_uuid ],
            'bindTypes' => [ 'id' => Column::BIND_PARAM_STR ],
        ]))) {
            return null;
        }
        return $track;
    }

    protected function log(string $msg): void
    {
        $this->getLogger()->info(sprintf('%s: %s', __CLASS__, $msg));
    }

    protected function getLogger(): Logger
    {
        return $this->getDI()->getShared(LoggerProvider::NAME);
//        return $this->logger;
    }
}
