<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Discoveryfy\Constants\CacheKeys;
use Phalcon\Api\Tasks\BaseTask;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Cache\Adapter\Redis as RedisCacheAdapter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function in_array;
use function Phalcon\Api\Core\appPath;
use const PHP_EOL;

/**
 * Class ClearCacheTask
 *
 * @property Cache $cache
 * @property Config $config
 */
class ClearCacheTask extends BaseTask
{
    protected $options = [
        'files', 'redis', 'redis-flush', 'default', 'all' //, 'memcached'
    ];

    public function helpAction()
    {
        echo $this->formatTitle('Clear Cache');
        echo 'Available options: ' . implode(', ', $this->options) . PHP_EOL;
        parent::helpAction();
    }

    /**
     * Clears the data cache from the application
     */
    public function runAction(string $option = 'default')
    {
        echo $this->formatTitle('Running Clear Cache');

        if (!in_array($option, $this->options)) {
            throw new \InvalidArgumentException(sprintf('Invalid option %s. Valid Options: %s', $option, implode(', ', $this->options)));
        }
        if (in_array($option, ['files', 'default', 'all'])) {
            $this->clearFileCache();
        }
        if (in_array($option, ['redis', 'default', 'all'])) {
            $this->clearRedis();
        }
        if (in_array($option, ['redis-flush', 'all'])) {
            $this->flushRedis();
        }
//        if (in_array($option, ['memcached', 'all'])) {
//            $this->clearMemCached();
//        }
    }

    /**
     * Clears file based cache
     */
    private function clearFileCache()
    {
        echo $this->format('Deleting files in Cache folder', 'yellow') . PHP_EOL;

        $fileList    = [];
        $whitelist   = ['.', '..', '.gitignore'];
        $path        = appPath('storage/cache');
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator    = new RecursiveIteratorIterator(
            $dirIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /**
         * Get how many files we have there and where they are
         */
        foreach ($iterator as $file) {
            if (true !== $file->isDir() && true !== in_array($file->getFilename(), $whitelist)) {
                $fileList[] = $file->getPathname();
            }
        }

        echo $this->format(sprintf('Found %s files: ', count($fileList)), 'yellow');
        if (count($fileList) > 0) {
            foreach ($fileList as $file) {
                $this->formatResult(unlink($file));
            }
            echo PHP_EOL;
        }

        echo $this->format('All cache files deleted', 'green') . PHP_EOL . PHP_EOL;
    }

    private function clearRedis()
    {
        echo $this->format('Clearing Redis cache', 'yellow') . PHP_EOL;

        // Remove only some keys
        $keys_prefix = [
            CacheKeys::LOGIN_CSRF,
            CacheKeys::REGISTER_CSRF,
            CacheKeys::JWT,
            CacheKeys::MODEL,
            CacheKeys::QUERY
        ];
        $adapter = $this->cache->getAdapter();
        if (!($adapter instanceof RedisCacheAdapter)) {
            throw new \Exception('The Phalcon Cache is not based in Redis');
        }
        foreach ($keys_prefix as $key_prefix) {
            $keys = $adapter->getKeys($key_prefix);
            echo $this->format(sprintf('The prefix %s has %s keys: ', $key_prefix, count($keys)), 'yellow');
            foreach ($keys as $key) {
                echo $key.PHP_EOL;
                $this->formatResult($adapter->delete($key));
                $this->formatResult($this->cache->delete($key));
            }
            echo PHP_EOL;
        }

        echo $this->format('Redis cache cleared', 'green') . PHP_EOL . PHP_EOL;
    }

    private function flushRedis()
    {
        echo $this->format('Flushing Redis cache', 'yellow') . PHP_EOL;
        echo 'Removing all content from redis: ' . $this->formatResult(
            $this->cache->clear()
        );
        echo $this->format('Redis cache flushed', 'green') . PHP_EOL . PHP_EOL;
    }

    /**
     * Clears memcached data cache
     */
    private function clearMemCached()
    {
        echo $this->format('Deleting all MemCached content', 'yellow') . PHP_EOL;

        $default = [
            'servers'  => [
                0 => [
                    'host'   => '127.0.0.1',
                    'port'   => 11211,
                    'weight' => 100,
                ],
            ],
            'client'   => [
                \Memcached::OPT_PREFIX_KEY => 'api-',
            ],
            'lifetime' => 86400,
            'prefix'   => 'data-',
        ];

        $options = $this->config->path('cache.options.libmemcached', null);
        if (true !== empty($options)) {
            $options = $options->toArray();
        } else {
            $options = $default;
        }

        $servers   = $options['servers'] ?? [];
        $memcached = new \Memcached();
        foreach ($servers as $server) {
            $memcached->addServer($server['host'], $server['port'], $server['weight']);
        }

        $keys = $memcached->getAllKeys();
        // 7.2 countable
        $keys = $keys ?: [];
        echo sprintf('Found %s keys', count($keys)) . PHP_EOL;
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                if ('api-data' === substr($key, 0, 8)) {
                    $server     = $memcached->getServerByKey($key);
                    $result     = $memcached->deleteByKey($server['host'], $key);
                    $resultCode = $memcached->getResultCode();
                    $resultTest = (true === $result && $resultCode !== \Memcached::RES_NOTFOUND);
                    $this->formatResult($resultTest);
                }
            }
            echo PHP_EOL;
        }

        echo $this->format('Cleared MemCached content', 'green') . PHP_EOL . PHP_EOL;
    }
}
