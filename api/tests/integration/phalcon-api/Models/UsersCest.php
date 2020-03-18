<?php

namespace Discoveryfy\Tests\integration\Phalcon\Api\Models;

use Discoveryfy\Models\Users;
use Discoveryfy\Tests\integration\Phalcon\Api\BaseCest;
use IntegrationTester;
use Phalcon\Api\Filters\UUIDFilter;
use Phalcon\Filter;
use Phalcon\Security\Random;

class UsersCest extends BaseCest
{
    public function validateModel(IntegrationTester $I)
    {
        $I->haveModelDefinition($this->getDefaultModel(), array_keys($this->getDefaultModelAttributes()));
    }

    public function validateFilters(IntegrationTester $I)
    {
        $model    = new Users();
        $expected = [
            'id'                => UUIDFilter::FILTER_NAME,
            'enabled'           => Filter::FILTER_BOOL,
            'password'          => Filter::FILTER_STRING,
            'created_at'        => Filter::FILTER_STRING,
            'updated_at'        => Filter::FILTER_STRING,
            'username'          => Filter::FILTER_STRING,
            'email'             => Filter::FILTER_EMAIL,
            'public_visibility' => Filter::FILTER_BOOL,
            'public_email'      => Filter::FILTER_BOOL,
            'language'          => Filter::FILTER_STRING,
            'theme'             => Filter::FILTER_STRING,
            'rol'               => Filter::FILTER_STRING,
        ];
        $I->assertEquals($expected, $model->getAttributes());
    }

    public function checkValidationIdInvalidString(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('id', 'a-a');
        $user->set('username', 'test_invalid_id'); //Avoid 'This username already exists in the database' validation
        $I->assertFalse($user->validation(), 'Invalid string');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('Invalid id', ($msgs[0])->getMessage());
//        $I->assertEquals('This username already exists in the database', ($msgs[0])->getMessage());
    }

    public function checkValidationIdInvalidNumeric(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('id', 1234);
        $user->set('username', 'test_invalid_id'); //Avoid 'This username already exists in the database' validation
        $I->assertFalse($user->validation(), 'Invalid id');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('Invalid id', ($msgs[0])->getMessage());
    }

    public function checkValidationIdValid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('id',  (new Random())->uuid());
        $user->set('username', 'test_valid_id'); //Avoid 'This username already exists in the database' validation
        $I->assertTrue($user->validation(), 'Valid id');
        $I->assertCount(0, $user->getMessages());
    }

    public function checkValidationUsernameInvalid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('username', 'a');
        $I->assertFalse($user->validation(), 'Invalid username');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('The username must have minimum 4 characters', ($msgs[0])->getMessage());
    }

    public function checkValidationUsernameInvalidUniqueness(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('username', 'testuser');
        $I->assertFalse($user->validation(), 'Invalid username');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('This username already exists in the database', ($msgs[0])->getMessage());
    }

    public function checkValidationUsernameValid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('username', 'testuser_testuser');
        $I->assertTrue($user->validation(), 'Valid username');
        $I->assertCount(0, $user->getMessages());
    }

    public function checkValidationEmailInvalid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('email', 'a');
        $user->set('username', 'test_invalid_email'); //Avoid 'This username already exists in the database' validation
        $I->assertFalse($user->validation(), 'Invalid email');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('The email field is not valid', ($msgs[0])->getMessage());
    }

    public function checkValidationEmailValid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('email', 'test@test.com');
        $user->set('username', 'test_invalid_email'); //Avoid 'This username already exists in the database' validation
        $I->assertTrue($user->validation(), 'Valid email');
        $I->assertCount(0, $user->getMessages());
    }

    public function checkValidationLanguageInvalid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('language', 'a');
        $I->assertFalse($user->validation(), 'Invalid language');
        $msgs = $user->getMessages();
        $I->assertCount(1, $msgs);
        $I->assertEquals('Field language must be a part of list: en, es, ca', ($msgs[0])->getMessage());
    }

    public function checkValidationLanguageValid(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($this->getDefaultModel(), $this->getDefaultModelAttributes());

        $user->set('language', 'en');
        $I->assertTrue($user->validation(), 'Valid language');
        $I->assertCount(0, $user->getMessages());

//        'language'          => [ 'en', 'es', 'ca' ],
//        'theme'             => [ 'default' ],
//        'rol'               => [ 'ROLE_ADMIN', 'ROLE_USER' ]
    }

    public function validateRelationships(IntegrationTester $I)
    {
        $actual = $I->getModelRelationships(Users::class);
        $I->assertCount(0, $actual);
    }
}
