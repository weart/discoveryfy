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

use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

return [
    'app'        => [
        'version'      => envValue('VERSION', time()),
        'timezone'     => envValue('APP_TIMEZONE', 'UTC'),
        'debug'        => (bool) envValue('APP_DEBUG', false),
        'env'          => envValue('APP_ENV', 'development'),
        'devMode'      => (bool)('development' === envValue('APP_ENV', 'development')),
        'baseUri'      => envValue('APP_BASE_URI'),
        'supportEmail' => envValue('APP_SUPPORT_EMAIL'),
        'time'         => microtime(true),
        'privateKey'   => envValue('PRIVATE_KEY', appPath('config/jwt.pem')),
        'sessionTTL'   => (int) envValue('SESSION_TTL', 604800) //1 week
    ],
    'db'         => [
        'timestamps_from_db' => false
    ],
    'cache'      => [
        'adapter' => 'redis',
        'options' => [
            'host' => envValue('REDIS_HOST', 'cache'), //By default: 127.0.0.1
            'port' => envValue('REDIS_PORT', 6379), //By default: 6379
//            'index' => 1,
//            'persistent'   => false,
            'prefix'   => envValue('CACHE_PREFIX', ''), //By default: ph-reds-
            'lifetime'   => (int) envValue('CACHE_LIFETIME', 86400), //Two days
//            'defaultSerializer' => 'Php',
//            'serializer' => null,
        ],
//        'adapter' => 'memory',
//        'options' => [
//            'cacheDir' => appPath('storage/cache/data/'),
//            'prefix'   => 'data-',
//        ],
//        'adapter' => 'libmemcached',
//        'options' => [
//            'libmemcached' => [
//                'servers'  => [
//                    0 => [
//                        'host'   => envValue('DATA_API_MEMCACHED_HOST', '127.0.0.1'),
//                        'port'   => envValue('DATA_API_MEMCACHED_PORT', 11211),
//                        'weight' => envValue('DATA_API_MEMCACHED_WEIGHT', 100),
//                    ],
//                ],
//                'client'   => [
//                    \Memcached::OPT_PREFIX_KEY => 'api-',
//                ],
//                'lifetime' => envValue('CACHE_LIFETIME', 86400),
//                'prefix'   => 'data-',
//            ],
//        ],

        'metadata' => [
            'dev'  => [
                'adapter' => 'Memory',
                'options' => [],
            ],
            'prod' => [
                'adapter' => 'Files',
                'options' => [
                    'metaDataDir' => appPath('storage/cache/metadata/'),
                ],
            ],
        ],
    ],
    'routers' => [
        \Discoveryfy\Routes\PublicRoutes::class,
        \Discoveryfy\Routes\UserRoutes::class,
        \Discoveryfy\Routes\GroupsRoutes::class,
        \Discoveryfy\Routes\PollsRoutes::class,
        \Discoveryfy\Routes\TestRoutes::class,
    ],
    'public_routes' => [
        '/login', '/register'
    ]
];
