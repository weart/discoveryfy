<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Discoveryfy\Services\SpotifyService;

class SpotifyProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'spotify';

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(self::NAME, function () use ($container) {
            $spotify = new SpotifyService();
            $spotify->setDI($container);
            return $spotify;
        });
    }
}
