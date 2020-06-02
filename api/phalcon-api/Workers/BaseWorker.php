<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Workers;

use Discoveryfy\Providers\SpotifyProvider;
use Discoveryfy\Services\SpotifyService;
use Interop\Queue\Message;
//use Enqueue\Redis\RedisMessage as Message;
use Phalcon\Di\Injectable;
use Interop\Queue\Processor;
use SpotifyWebAPI\SpotifyWebAPI;

abstract class BaseWorker extends Injectable implements Processor
{
    protected $max_attemps = 3;

    protected function returnResult(bool $success, int $attempts, bool $retry = true)
    {
        if ($success) {
            return self::ACK;
        }
        if ($retry && $attempts <= $this->max_attemps) {
            return self::REQUEUE;
        }
        return self::REJECT;
    }

    protected function incrementAttempt(Message $message)
    {
        $message->setHeader('attempts', ($message->getAttempts()+1));
        return $message->getAttempts();
    }

    protected function getSpotifyService(): SpotifyService
    {
        return $this->getDI()->getShared(SpotifyProvider::NAME);
//        return $this->spotify;
    }

    protected function getSpotifyApi(): SpotifyWebAPI
    {
        return $this->getSpotifyService()->getApi();
    }
}
