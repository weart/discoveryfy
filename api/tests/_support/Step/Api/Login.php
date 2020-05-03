<?php
namespace Step\Api;

use Codeception\Exception\TestRuntimeException;
use Codeception\Util\HttpCode;
use Codeception\Util\JsonArray;
use Page\Data;
use function json_decode;

class Login extends \ApiTester
{

    public function loginAsAnon()
    {
        $this->comment('Do login as anon user');
        return $this->getAuthTokenJson();
    }

    public function loginAsTest()
    {
        $this->comment('Do login as test user');
        $I = $this;
        $I->haveRecordWithFields($I->getDefaultModel(), $I->getDefaultModelAttributes());
        return $I->getAuthTokenJson(Data::loginJson());
    }

    public function loginAsAdmin()
    {
        $this->comment('Do login as admin user');
        if (false === getenv('SEED_ROOT_USER') || false === getenv('SEED_ROOT_PASS')) {
            throw new TestRuntimeException('Admin credentials not setted');
        }
        return $this->getAuthTokenJson([
            'username'          => getenv('SEED_ROOT_USER'),
            'password'          => getenv('SEED_ROOT_PASS'),
        ]);
    }

    public function getLoginCSRFTokenJson(): string
    {
        $I = $this;
        $I->setContentType('application/json');
        $I->sendGET(Data::$loginUrl);
        $I->removeContentType();

        $I->seeResponseIsValidJson();
        return trim($I->grabResponse(), '"');
    }

    public function getLoginCSRFTokenJsonApi()
    {
        $I = $this;
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$loginUrl);
        $I->removeContentType();

        $I->seeResponseIsValidJsonApi(
            HttpCode::OK,
            [
                'type' => 'string',
                'id' => 'string'
            ],
            [
                'type' => 'CSRF'
            ]
        );
        return $I->grabDataFromResponseByJsonPath('$.data.id')[0];
    }

    public function getAuthTokenJson($credentials = null): array
    {
        $I = $this;
        $I->haveHttpHeader('X-CSRF-TOKEN', $I->getLoginCSRFTokenJson());
        $I->setContentType('application/json');
        $I->sendPOST(Data::$loginUrl, $credentials);
        $I->removeContentType();

        $I->seeResponseIsValidJson(HttpCode::OK);
        $I->seeResponseContainsJson(['type' => 'jwt']);
        $I->seeResponseContainsJson(['type' => 'sessions']);
        $resp = (new JsonArray($I->grabResponse()))->toArray();
        return $I->getLoginData($resp);
    }

    public function getAuthTokenJsonApi($credentials = null): array
    {
        $I = $this;
        $I->haveHttpHeader('X-CSRF-TOKEN', $I->getLoginCSRFTokenJsonApi());
        $I->setContentType('application/vnd.api+json');
        $I->sendPOST(Data::$loginUrl, $credentials);
        $I->removeContentType();

        $I->seeResponseIsValidJsonApi(HttpCode::OK);
        $I->seeResponseContainsJsonKey('data', ['type' => 'jwt']);
        $I->seeResponseContainsJsonKey('data', ['type' => 'sessions']);
//        $I->seeResponseContainsJsonKey('data', ['type' => 'users']);
        return $I->getLoginData($I->grabDataFromResponseByJsonPath('$.data')[0]);
    }

    private function getLoginData(array $data)
    {
        $jwt = $session_id = $user_id = null;
        foreach ($data as $obj) {
            if ($obj['type'] === 'jwt') {
                $jwt = $obj['id'];
            } else if ($obj['type'] === 'users') {
                $user_id = $obj['id'];
            } else if ($obj['type'] === 'sessions') {
                $session_id = $obj['id'];
            }
        }
        if (empty($jwt) || empty($session_id)) { //Anon doesnt have $user_id || empty($user_id)
            throw new TestRuntimeException('Invalid login');
        }
        return [$jwt, $session_id, $user_id];
    }

    public function setContentType($content_type = 'application/json')
    {
        $I = $this;
        $I->haveHttpHeader('Content-Type', $content_type);
        $I->haveHttpHeader('accept', $content_type);
    }

    public function removeContentType()
    {
        $I = $this;
        $I->deleteHeader('Content-Type');
        $I->deleteHeader('accept');
    }

    public function testCSRFToken(string $token)
    {
        $I = $this;
        $I->assertIsString($token, 'Token is a string');
        $I->assertRegExp('/^[a-zA-Z0-9\-\_]{32}$/', $token, 'Token must be valid');
    }

    public function testJWTToken(string $jwt)
    {
        $I = $this;
        $I->assertRegExp(
            '/^[A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+\.?[A-Za-z0-9-_.+\/=]*$/',
            $jwt,
            'Generic jwt regexp'
        );
        $I->assertRegExp(
            '/^[A-Za-z0-9-_=]{96}\.[A-Za-z0-9-_=]{215}\.[A-Za-z0-9-_.+\/=]{86}$/',
            $jwt,
            'Current jwt regexp'
        );
    }

    public function testUUID(string $uuid)
    {
        $I = $this;
        $I->assertRegExp(
            '/^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$/',
            $uuid,
            'UUID not valid'
        );
    }
}
