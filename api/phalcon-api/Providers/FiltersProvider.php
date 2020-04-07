<?php
declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Api\Filters\UUIDFilter;
//use Phalcon\Api\Filters\EnumFilter;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Filter;

class FiltersProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'filter';

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $filters = $container->get(self::NAME);
        /** @var Filter $filters */
        $filters->set(UUIDFilter::FILTER_NAME, UUIDFilter::class);
//        $filters->set(EnumFilter::FILTER_NAME, EnumFilter::class);
        $container->setShared(self::NAME, $filters);
    }
}
