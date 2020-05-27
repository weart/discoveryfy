<?php
declare(strict_types=1);

namespace Phalcon\Api\Providers;

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
