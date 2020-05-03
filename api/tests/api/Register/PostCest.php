<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Register;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class RegisterPostCest
{
    public function registerUserJson(Login $I, RegisterGetCest $registerGet)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $registerGet->getRegisterCSRFTokenJson($I));
        $I->setContentType('application/json');
        $user_data = Data::registerJson();
        $I->sendPOST(Data::$registerUrl, $user_data);

        $I->seeResponseIsValidJson(
            HttpCode::CREATED,
            [
                'id'                            => 'string:!empty', //'016aeb55-7ecf-4862-a229-dd7478b17537'
                'attributes.created_at'         => 'string:date', //'2020-03-23 11:57:46'
//                'attributes.updated_at'         => 'string:date|null', //''
                'links.self'                    => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ],
            [
                'type'                          => 'users',
                'attributes.username'           => $user_data['username'],
                'attributes.email'              => $user_data['email'],
                'attributes.public_visibility'  => $user_data['public_visibility'],
                'attributes.public_email'       => $user_data['public_email'],
                'attributes.language'           => $user_data['language'],
                'attributes.theme'              => $user_data['theme'],
                'attributes.rol'                => 'ROLE_USER',
                'attributes.updated_at'         => '',
            ]
        );
    }

    public function registerUserJsonApi(Login $I, RegisterGetCest $registerGet)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $registerGet->getRegisterCSRFTokenJsonApi($I));
        $I->setContentType('application/vnd.api+json');
        $user_data = Data::registerJson();
        $I->sendPOST(Data::$registerUrl, $user_data);

        $I->seeResponseIsValidJsonApi(
            HttpCode::CREATED,
            [
                'id'                            => 'string:!empty', //'016aeb55-7ecf-4862-a229-dd7478b17537'
                'attributes' => [
                    'email'                     => 'string:email',
                    'created_at'                => 'string:date', //'2020-03-23 11:57:46'
//                    'updated_at'                => 'string:date|null', //''
                ],
                'links' => [
                    'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
                ]
            ],
            [
                'type'                          => 'users',
                'attributes' => [
                    'username'                  => $user_data['username'],
                    'email'                     => $user_data['email'],
                    'public_visibility'         => $user_data['public_visibility'],
                    'public_email'              => $user_data['public_email'],
                    'language'                  => $user_data['language'],
                    'theme'                     => $user_data['theme'],
                    'rol'                       => 'ROLE_USER',
                    'updated_at'                => '',
                ]
            ]
        );
    }
}
