<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Discoveryfy\Constants\CacheKeys;
use Enqueue\Redis\RedisContext as Queue;
use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\QueueProvider;
use Phalcon\Api\Tasks\BaseTask;
use Phalcon\Cache;
use const PHP_EOL;

class JobStatsTask extends BaseTask
{
    public function helpAction()
    {
        echo $this->formatTitle('Job Stats');
        echo 'Get information about the jobs in the queue' . PHP_EOL;
        parent::helpAction();
    }

    public function runAction()
    {
        echo $this->formatTitle('Running Job Stats');
        $redis = $this->getRedisFromCache();

        $num_jobs = $redis->lLen(CacheKeys::getJobQueue());
        echo 'Pending jobs: '.$this->format((string) $num_jobs, 'green').PHP_EOL;
        // Number of workers?
        // More stats: success, failed...
    }

//    protected function getRedisFromQueue(): \Enqueue\Redis\Redis
//    {
//        return $this->getQueueService()->getRedis();
//    }

    protected function getRedisFromCache(): \Redis
    {
        /** @var \Redis $redis */
        $redis = $this->getCacheService()->getAdapter()->getAdapter();
        // Phalcon sets a default prefix set in options
        $redis->setOption(\Redis::OPT_PREFIX, '');
        return $redis;
    }

    protected function getCacheService(): Cache
    {
        return $this->getDI()->getShared(CacheDataProvider::NAME);
//        return $this->cache;
    }

//    protected function getQueueService(): Queue
//    {
//        return $this->getDI()->getShared(QueueProvider::NAME);
////        return $this->queue;
//    }
}
