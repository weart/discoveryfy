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

namespace Phalcon\Api\Providers;

use Phalcon\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Storage\SerializerFactory;
use function Phalcon\Api\Core\appPath;

class ModelsCacheProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'modelsCache';

    /**
     * Registers a service provider.
     *
     * @param DiInterface $di
     */
    public function register(DiInterface $di): void
    {
        /*
        $di->setShared(self::NAME, function () {
            $instanceName = 'stream';
            $options = [
                'defaultSerializer' => 'Json',
                'lifetime'          => 7200,
                'storageDir'        => appPath('storage/cache/data'),
            ];

            $serializerFactory = new SerializerFactory();
            $adapterFactory = new AdapterFactory($serializerFactory);
            $adapter = $adapterFactory->newInstance($instanceName, $options);

            return new Cache($adapter);
        });
        */
        $di->setShared(self::NAME, $di->getShared(CacheDataProvider::NAME));
    }
}
