<?php

use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\Util\HttpCode;
use Discoveryfy\Models\Users;
use Page\Data as DataPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;

    /**
     * Checks if the response is JSON was successful
     */
    public function seeResponseIsJsonSuccessful($code = HttpCode::OK)
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs($code);
    }

    /**
     * Checks if the response is JSONAPI was successful
     * @param int $code
     */
    public function seeResponseIsJsonApiSuccessful($code = HttpCode::OK)
    {
        $this->seeResponseIsJsonSuccessful($code);
        $this->seeResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string'
                ],
                'data'    => 'array',
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );

        $this->checkHash();
    }

    /**
     * Checks if the JSONAPI error response is formatted correctly
     *
     * @param array $errors array with status (mandatory), and code & title (optional)
     * @param int $httpCode
     */
    public function seeResponseIsJsonApiErrors(array $errors, int $httpCode = HttpCode::BAD_REQUEST)
    {
        $this->seeResponseIsJsonSuccessful($httpCode);
        $this->seeResponseMatchesJsonType(
            [
                'jsonapi' => [
                    'version' => 'string'
                ],
                'errors'  => 'array',
                'meta'    => [
                    'timestamp' => 'string:date',
                    'hash'      => 'string',
                ]
            ]
        );
        $this->checkHash();

        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $num_errors = count($response['errors']);
        $this->assertCount($num_errors, $errors, 'Different number of errors');
        for ($i = 0; $i < $num_errors; $i++) {
            $test_error = $errors[$i];
            $resp_error = $response['errors'][$i];

            if (!isset($test_error['status'])) {
                throw new \Phalcon\Exception('Invalid error');
            }
            $httpCode = $test_error['status'];
            $appCode = $test_error['code'] ?? $httpCode;
            $title = $test_error['title'] ?? HttpCode::getDescription($httpCode);
            $this->assertEquals($httpCode, $resp_error['status'], 'This value should express HTTP status code applicable to this problem, expressed as a string value');
            $this->assertEquals($appCode, $resp_error['code'], 'This value should express an application-specific error code, expressed as a string value');
            $this->assertEquals($title, $resp_error['title'], 'This value should express a short, human-readable summary of the problem');
        }
    }

    /**
     * Checks if the JSONAPI error response is formatted correctly with only one error
     *
     * @param int           $httpCode
     * @param string|null   $title
     * @param int|null      $appCode
     */
    public function seeResponseIsJsonApiError(int $httpCode = HttpCode::BAD_REQUEST, ?string $title = null, ?int $appCode = null)
    {
        $this->seeResponseIsJsonApiErrors(
            [
                [
                    'status' => $httpCode,
                    'code' => $appCode,
                    'title' => $title,
                ]
            ],
            $httpCode
        );
    }

    /**
     * Checks if the response was successful
     */
//    public function seeResponseIs400()
//    {
//        $this->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, '400 (Bad Request)');
//    }

    /**
     * Checks if the response was successful
     */
    public function seeResponseIs404()
    {
        $this->seeResponseIsJsonApiError(HttpCode::NOT_FOUND, '404 (Not Found)');
    }

    private function checkHash()
    {
        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        unset($response['meta'], $response['jsonapi']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));
    }

    public function seeSuccessJsonResponse(string $key = 'data', array $data = [])
    {
        $this->seeResponseContainsJson([$key => $data]);
    }

//    public function apiLogin()
//    {
//        $this->deleteHeader('Authorization');
//        $this->sendPOST(DataPage::$loginUrl, DataPage::loginJson());
//        $this->seeResponseIsJsonApiSuccessful();
//
//        $response = $this->grabResponse();
//        $response  = json_decode($response, true);
//        $data      = $response['data'];
//        $token     = $data['token'];
//
//        return $token;
//    }

//    public function addApiUserRecord()
//    {
//        return $this->haveRecordWithFields(
//            Users::class,
//            [
//                'status'        => 1,
//                'username'      => 'testuser',
//                'password'      => 'testpassword',
//                'issuer'        => 'https://niden.net',
//                'tokenPassword' => '12345',
//                'tokenId'       => '110011',
//            ]
//        );
//    }
}
