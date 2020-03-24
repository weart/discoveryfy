<?php

namespace Discoveryfy\Tests\integration\Phalcon\Api\Transformers;

use Discoveryfy\Exceptions\ModelException;
use Discoveryfy\Models\Users;
use IntegrationTester;
use Phalcon\Api\Transformers\BaseTransformer;

class BaseTransformerCest
{
    /**
     * @param IntegrationTester $I
     *
     * @throws ModelException
     */
    public function checkTransformer(IntegrationTester $I)
    {
        /** @var Users $user */
        $user = $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());

        $transformer = new BaseTransformer();
        $expected    = [
            //Private attributes
//            'enabled'           => $user->get('enabled'),
//            'password'          => $user->get('password'),
            // Public attributes
            'id'                => $user->get('id'),
//            'created_at'        => $user->get('created_at'),
//            'updated_at'        => $user->get('updated_at'),
            'created_at'        => $user->getCreatedAt()->format(\DateTime::ATOM),
            'updated_at'        => $user->getUpdatedAt()->format(\DateTime::ATOM),
            'username'          => $user->get('username'),
            'email'             => $user->get('email'),
            'public_visibility' => $user->get('public_visibility'),
            'public_email'      => $user->get('public_email'),
            'language'          => $user->get('language'),
            'theme'             => $user->get('theme'),
            'rol'               => $user->get('rol'),
        ];

        $I->assertEquals($expected, $transformer->transform($user));
    }
}
