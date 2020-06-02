<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Providers;

use Phalcon\Api\Queue\JobManager;
use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;

class JobsProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'jobs';

    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Config $config */
        $config = $container->getShared(ConfigProvider::NAME);
        /** @var QueueProvider $queue */
        $queue = $container->getShared(QueueProvider::NAME);
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');

        $jobManager = new JobManager();
        $jobManager->setDI($container);
//        $jobManager->attach($eventsManager);

        $container->setShared(self::NAME, $jobManager);
    }
}

