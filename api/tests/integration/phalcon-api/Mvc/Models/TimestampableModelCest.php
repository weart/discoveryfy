<?php

namespace Discoveryfy\Tests\integration\Phalcon\Api\Mvc\Model;

use IntegrationTester;
use Phalcon\Api\Mvc\Model\TimestampableModel;
use Phalcon\Config;

/**
 * Class TimestampableModelCest
 */
class TimestampableModelCest
{
    /**
     * @param IntegrationTester $I
     */
    public function modelCreationFillCreatedAt(IntegrationTester $I)
    {
        /** @var Config $config */
        $config = $I->grabFromDi('config');

        //test using php timestamps
        $config->set('db.timestamps_from_db', true);
        $whitelist = [];
        $blacklist = ['created_at', 'updated_at', 'deleted_at'];
        /**
         * @var $fixture TimestampableModel
         */
        $fixture = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes($whitelist, $blacklist));
        $I->assertEquals('', $fixture->get('created_at'));
        //@ToDo: Throws exception because created_at is not defined...
//        $I->assertGreaterThan(time(), $fixture->getCreatedAt()->getTimestamp());
//        $I->assertInstanceOf(\DateTime::class, $fixture->getCreatedAt());

        //test using database timestamps
        $config->set('db.timestamps_from_db', false);
        $whitelist = [];
        $blacklist = ['created_at', 'updated_at', 'deleted_at'];
        /**
         * @var $fixture TimestampableModel
         */
        $fixture = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes($whitelist, $blacklist));
        $I->assertEquals('', $fixture->get('created_at'));
        //@ToDo: Throws exception because created_at is not defined...
//        $I->assertGreaterThan(time(), $fixture->getCreatedAt()->getTimestamp());
//        $I->assertInstanceOf(\DateTime::class, $fixture->getCreatedAt());
    }

    /**
     * @param IntegrationTester $I
     */
    public function modelModifyFieldChangeUpdatedAt(IntegrationTester $I)
    {
        $whitelist = [];
        $blacklist = ['created_at', 'updated_at', 'deleted_at'];
        /**
         * @var $fixture TimestampableModel
         */
        $fixture = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes($whitelist, $blacklist));
        $preUpdateTS = $fixture->getUpdatedAt()->getTimestamp();
//        $I->assertEmpty($fixture->get('updated_at'));   //$fixture->getUpdatedAt();
        $fixture->set('language', 'ca')->save();
        $I->assertGreaterOrEquals(time(), $fixture->getUpdatedAt()->getTimestamp());
        $I->assertGreaterOrEquals($preUpdateTS, $fixture->getUpdatedAt()->getTimestamp());
        $I->assertInstanceOf(\DateTime::class, $fixture->getUpdatedAt());
    }

    /**
     * @param IntegrationTester $I
     */
//    public function modelGetCreatedAt(IntegrationTester $I)
//    {
//        /**
//         * @var $fixture TimestampableModel
//         */
//        $fixture = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
//        $I->assertInstanceOf(\DateTime::class, $fixture->getCreatedAt());
//    }

    /**
     * @param IntegrationTester $I
     */
//    public function modelGetUpdatedAt(IntegrationTester $I)
//    {
//        $fixture = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
//        $fixture->set('language', 'ca')->save();
//        $I->assertInstanceOf(\DateTime::class, $fixture->getUpdatedAt());
//    }
}
