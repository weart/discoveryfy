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

use Phalcon\Api\Providers\CacheDataProvider;
use Phalcon\Api\Providers\ConfigProvider;
use Phalcon\Api\Providers\DatabaseProvider;
use Phalcon\Api\Providers\ErrorHandlerProvider;
use Phalcon\Api\Providers\LoggerProvider;
use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Api\Providers\ModelsCacheProvider;
use Phalcon\Api\Providers\RequestProvider;
use Phalcon\Api\Providers\ResponseProvider;
use Phalcon\Api\Providers\RouterProvider;
use Phalcon\Api\Providers\FiltersProvider;
use Phalcon\Api\Providers\SecurityProvider;
use Phalcon\Api\Providers\AuthProvider;
use Phalcon\Api\Providers\QueueProvider;
use Phalcon\Api\Providers\JobsProvider;
use Discoveryfy\Providers\SpotifyProvider;

/**
 * Enabled providers. Order does matter
 */
return [
    ConfigProvider::class,
    LoggerProvider::class,
    ErrorHandlerProvider::class,
    DatabaseProvider::class,
    ModelsMetadataProvider::class,
    RequestProvider::class,
    ResponseProvider::class,
    RouterProvider::class,
    CacheDataProvider::class,
    ModelsCacheProvider::class,
    FiltersProvider::class,
    SecurityProvider::class,
    AuthProvider::class,
    SpotifyProvider::class,
    QueueProvider::class,
    JobsProvider::class
];
