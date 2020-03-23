<?php

namespace Discoveryfy\Tests\api\Register;

use ApiTester;
//use Discoveryfy\Models\Users;
use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Api\Http\Response;
use function json_decode;

class RegisterPostCest
{
    public function registerUserJson(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $user_data = Data::registerJson();
        $I->sendPOST(Data::$registerUrl, $user_data);

        $I->seeResponseCodeIs(Response::CREATED);
        $I->cantSeeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
        $I->seeResponseContainsJson([
            'type'                          => 'user',
            'attributes.username'           => $user_data['username'],
            'attributes.email'              => $user_data['email'],
            'attributes.public_visibility'  => $user_data['public-visibility'],
            'attributes.public_email'       => $user_data['public-email'],
            'attributes.language'           => $user_data['language'],
            'attributes.theme'              => $user_data['theme'],
            'attributes.rol'                => 'ROLE_USER',
            'attributes.updated_at'         => '',
        ]);
        $I->seeResponseMatchesJsonType([
            'id'                            => 'string', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes.created_at'         => 'string:date', //'2020-03-23 11:57:46'
//            'attributes.updated_at'         => 'string:date|null', //''
            'links.self'                    => 'string:url', //'https://api.discoveryfy.fabri...b17537'
        ]);
    }

    public function registerUserJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('X-CSRF-TOKEN', $this->getCSRFTokenJson($I));
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $user_data = Data::registerJson();
        $I->sendPOST(Data::$registerUrl, $user_data);

//        $I->seeResponseCodeIs(Response::CREATED);
//        $I->cantSeeResponseMatchesJsonType([
//            'errors' => 'array'
//        ]);
        $I->seeResponseIsJsonApiSuccessful(Response::CREATED);
        $I->seeSuccessJsonResponse('data', [
            'type'                          => 'user',
            'attributes' => [
                'username'                  => $user_data['username'],
                'email'                     => $user_data['email'],
                'public_visibility'         => $user_data['public-visibility'],
                'public_email'              => $user_data['public-email'],
                'language'                  => $user_data['language'],
                'theme'                     => $user_data['theme'],
                'rol'                       => 'ROLE_USER',
                'updated_at'                => '',
            ]
        ]);
        $I->seeResponseMatchesJsonType([
            'id'                            => 'string', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'email'                     => 'string:email',
                'created_at'                => 'string:date', //'2020-03-23 11:57:46'
//                'updated_at'                => 'string:date|null', //''
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]

        ], '$.data');
    }


    /**
     * Private functions
     */

    /**
     * The headers 'Content-Type' and 'accept' are removed in this function
     * @param ApiTester $I
     * @return string
     */
    private function getCSRFTokenJson(ApiTester $I): string
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET(Data::$registerUrl);
        $I->deleteHeader('Content-Type');
        $I->deleteHeader('accept');
        $I->dontSeeResponseContainsJson([
            'status' => 'error'
        ]);
        $I->seeResponseCodeIs(Response::OK);
        return trim($I->grabResponse(), '"');
    }
}
