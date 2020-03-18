<?php

namespace Discoveryfy\Tests\unit\Phalcon\Api\Providers;

use Phalcon\Api\Providers\ModelsMetadataProvider;
use Phalcon\Di\FactoryDefault;
//use Phalcon\Mvc\Model\MetaData\Libmemcached;
use Phalcon\Mvc\Model\MetaData\Memory;
use UnitTester;

class ModelsMetadataCest
{
    /**
     * @param UnitTester $I
     */
    public function checkRegistration(UnitTester $I)
    {
        $diContainer = new FactoryDefault();
        $provider    = new ModelsMetadataProvider();
        $provider->register($diContainer);

        $I->assertTrue($diContainer->has('modelsMetadata'));
        /** @var Memory $metadata */
        $metadata = $diContainer->getShared('modelsMetadata');
        $I->assertTrue($metadata instanceof Memory);
    }
}
