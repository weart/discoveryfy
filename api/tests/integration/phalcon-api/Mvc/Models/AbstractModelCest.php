<?php

namespace Discoveryfy\Tests\integration\Phalcon\Api\Mvc\Model;

use Codeception\Stub;
use Discoveryfy\Exceptions\ModelException;
use Discoveryfy\Models\Users;
use Exception;
use IntegrationTester;
use Monolog\Logger;
use Phalcon\Messages\Message;
use function Phalcon\Api\Core\appPath;

/**
 * Class AbstractModelCest
 */
class AbstractModelCest
{
    /**
     * @param IntegrationTester $I
     */
    public function modelGetSetFields(IntegrationTester $I)
    {
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
    }

    /**
     * Tests the model by setting a non existent field
     *
     * @param IntegrationTester $I
     */
    public function modelSetNonExistingFields(IntegrationTester $I)
    {
        $I->expectThrowable(
            ModelException::class,
            function () {
                $fixture = new Users();
                $fixture->set('id', '874c822c-7cda-440f-8d3e-35b0c8dbde5f')
                        ->set('some_field', true)
                        ->save()
                ;
            }
        );
    }

    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelGetNonExistingFields(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());

        $I->expectThrowable(
            ModelException::class,
            function () use ($user) {
                $user->get('some_field');
            }
        );
    }

    /**
     * Tests the model update interactions
     *
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function modelUpdateFields(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());

        $user->set('username', 'UPDATED_NAME')->save();

        $I->assertEquals($user->get('username'), 'UPDATED_NAME');

        $user->set('username', 'testuser')->save();
        $I->assertEquals($user->get('username'), 'testuser');
//        $I->assertEquals($user->get('password'), 'testpass');
//        $I->assertEquals($user->get('public_email'), true);
//        $I->assertEquals($user->get('rol'), 'ROLE_USER');
    }

    /**
     * @param IntegrationTester $I
     */
    public function modelUpdateFieldsNotSanitized(IntegrationTester $I)
    {
        /** @var Users $result */
        $user = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());

        $user->set('password', 'abcde\nfg')->save();
        $I->assertEquals($user->get('password'), 'abcde\nfg');

        /** Not sanitized */
        $user->set('password', 'abcde\nfg', false)->save();
        $I->assertEquals($user->get('password'), 'abcde\nfg');
    }

    /**
     * @param IntegrationTester $I
     */
    public function checkModelMessages(IntegrationTester $I)
    {
        $user = Stub::construct(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $result = $user
            ->set('username', 'test')
            ->save()
        ;
        $I->assertFalse($result);

        $I->assertEquals('error 1'.PHP_EOL.'error 2'.PHP_EOL, $user->getMessage());
    }

    /**
     * @param IntegrationTester $I
     * @throws Exception
     */
    public function checkModelMessagesWithLogger(IntegrationTester $I)
    {
        /** @var Logger $logger */
        $logger = $I->grabFromDi('logger');
        $user   = Stub::construct(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $fileName = appPath('storage/logs/api.log');
        $result   = $user
            ->set('username', 'test')
            ->save()
        ;
        $I->assertFalse($result);
        $I->assertEquals('error 1'.PHP_EOL.'error 2'.PHP_EOL, $user->getMessage());

        $user->getMessage($logger);

        $I->openFile($fileName);
        $I->seeInThisFile('error 1'.PHP_EOL);
        $I->seeInThisFile('error 2'.PHP_EOL);
    }
}
