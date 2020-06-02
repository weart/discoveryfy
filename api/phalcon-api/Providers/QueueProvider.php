<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Providers;

use Enqueue\Redis\RedisConnectionFactory;
use Enqueue\Redis\RedisContext;
use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class QueueProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'queue';

    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Config $config */
        $config = $container->getShared('config');

        /** @var RedisContext $queue */
        $queue = (new RedisConnectionFactory([
            'host' => $config->path('queue.host'),
            'port' => $config->path('queue.port'),
            'lazy' => true, //The connection will be performed as later as possible, if the option set to true
            'scheme_extensions' => ['phpredis'],
//            'persistent' => Specifies if the underlying connection resource should be left open when a script ends its lifecycle.
//            'timeout' => Timeout (expressed in seconds) used to connect to a Redis server after which an exception is thrown.
//            'read_write_timeout' => Timeout (expressed in seconds) used when performing read or write operations on the underlying network resource after which an exception is thrown.
//            'ssl' => could be any of http://fi2.php.net/manual/en/context.ssl.php#refsect1-context.ssl-options
//            'redelivery_delay' => Default 300 sec. Returns back message into the queue if message was not acknowledged or rejected after this delay.
//                             It could happen if consumer has failed with fatal error or even if message processing is slow and takes more than this time.
        ]))->createContext();

        $container->setShared(self::NAME, $queue);
    }
}

