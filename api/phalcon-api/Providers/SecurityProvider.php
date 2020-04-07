<?php
declare(strict_types=1);

namespace Phalcon\Api\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Security;

class SecurityProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'security';

    /**
     * {@inheritdoc}
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(self::NAME, function () {
            $security = new Security();
            // set Work factor (how many times we go through)
            $security->setWorkFactor(12); // can be a number from 1-12
            // set Default Hash
            $security->setDefaultHash(Security::CRYPT_BLOWFISH_Y); // choose default hash
            return $security;
        });
    }
}
