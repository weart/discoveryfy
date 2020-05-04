<?php

use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\PHPUnit\Constraint\JsonContains;
use Codeception\PHPUnit\Constraint\JsonType;
use Codeception\Util\HttpCode;
//use Discoveryfy\Models\Users;
//use Page\Data as DataPage;
use PHPUnit\Framework\Assert;

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
    /**
     * When $I->setContentType('application/json');
     * If is response return an error:
$I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg, HttpCode::UNAUTHORIZED);

     * If is response is successful:
$I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::groupResponseJsonType(), $content);

     * When $I->setContentType('application/vnd.api+json');
     * If is response return an error:
$I->seeResponseIsJsonApiError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg, HttpCode::UNAUTHORIZED);

     * If is response is successful:
$I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::groupResponseJsonType(), $content);
     */

    use _generated\ApiTesterActions;

    public function seeItemResponseIsJsonSuccessful($code = HttpCode::OK, array $jsonType = [], array $content = []): void
    {
        $this->seeResponseContainsNoErrors();
        $this->seeResponseIsJsonSuccessful($code);
        if (!empty($jsonType)) {
            $this->seeResponseMatchesJsonType($jsonType);
        }
        if (!empty($content)) {
            $this->seeResponseContainsJson($content);
        }
    }

    public function seeItemResponseIsJsonApiSuccessful(int $code = HttpCode::OK, array $jsonType = [], array $content = []): void
    {
        $this->seeResponseContainsNoErrors();
        $this->seeResponseIsJsonApiSuccessful($code);
        if (!empty($jsonType)) {
            $this->seeResponseMatchesJsonType($jsonType, '$.data');
        }
        if (!empty($content)) {
            $this->seeResponseContainsJsonKey('data', $content);
        }
    }

    public function seeCollectionResponseIsJsonSuccessful($code = HttpCode::OK, array $jsonType = [], array $content = []): void
    {
        $this->seeResponseContainsNoErrors();
        $this->seeResponseIsJsonSuccessful($code);
        if (!empty($jsonType) || !empty($content)) {
            $items = $this->grabDataFromResponseByJsonPath('$');
            foreach ($items as $item) {
                if (!empty($jsonType)) {
                    Assert::assertThat($item, new JsonType($jsonType));
//                    $this->assertThat($item, new JsonType($jsonType));
                }
                if (!empty($content)) {
                    Assert::assertThat(json_encode($item), new JsonContains($content)); //
//                    $this->assertThat($item, new JsonContains($content));
                }
            }
        }
    }

    public function seeCollectionResponseIsJsonApiSuccessful($code = HttpCode::OK, array $jsonType = [], array $content = []): void
    {
        $this->seeResponseContainsNoErrors();
        $this->seeResponseIsJsonSuccessful($code);
        if (!empty($jsonType) || !empty($content)) {
            $items = $this->grabDataFromResponseByJsonPath('$.data');
            foreach ($items as $item) {
                if (!empty($jsonType)) {
                    Assert::assertThat($item, new JsonType($jsonType));
//                    $this->assertThat($item, new JsonType($jsonType));
                }
                if (!empty($content)) {
                    Assert::assertThat(json_encode($item), new JsonContains($content));
//                    $this->assertThat($item, new JsonContains($content));
                }
            }
        }
    }

    public function seeResponseContainsNoErrors()
    {
        $this->dontSeeResponseContainsJson([
            'status'                => 'error'
        ]);
        $this->dontSeeResponseMatchesJsonType([
            'errors' => 'array'
        ]);
    }

    /**
     * Checks if the response is JSON was successful
     * @param int $code
     */
    public function seeResponseIsJsonSuccessful($code = HttpCode::OK): void
    {
        $this->seeResponseIsJson();
        $this->seeResponseCodeIs($code);
    }

    /**
     * Checks if the response is JSONAPI was successful
     * @param int $code
     */
    public function seeResponseIsJsonApiSuccessful($code = HttpCode::OK): void
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

    public function seeResponseIsJsonErrors(array $errors, int $httpCode = HttpCode::BAD_REQUEST): void
    {
        $this->seeResponseIsJsonSuccessful($httpCode);
        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $this->checkErrors($response['errors'], $errors);
    }

    public function seeResponseIsJsonError(int $httpCode = HttpCode::BAD_REQUEST, ?string $title = null, ?int $appCode = null): void
    {
        $this->seeResponseIsJsonErrors(
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
     * Checks if the JSONAPI error response is formatted correctly
     *
     * @param array $errors array with status (mandatory), and code & title (optional)
     * @param int $httpCode
     */
    public function seeResponseIsJsonApiErrors(array $errors, int $httpCode = HttpCode::BAD_REQUEST): void
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
        $this->checkErrors($response['errors'], $errors);
    }

    /**
     * Checks if the JSONAPI error response is formatted correctly with only one error
     *
     * @param int           $httpCode
     * @param string|null   $title
     * @param int|null      $appCode
     */
    public function seeResponseIsJsonApiError(int $httpCode = HttpCode::BAD_REQUEST, ?string $title = null, ?int $appCode = null): void
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

    private function checkErrors($resp_errors, $test_errors): void
    {
        $num_errors = count($resp_errors);
        $this->assertCount($num_errors, $test_errors, 'Different number of errors');
        for ($i = 0; $i < $num_errors; $i++) {
            $test_error = $test_errors[$i];
            $resp_error = $resp_errors[$i];

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

    private function checkHash()
    {
        $response  = $this->grabResponse();
        $response  = json_decode($response, true);
        $timestamp = $response['meta']['timestamp'];
        $hash      = $response['meta']['hash'];
        unset($response['meta'], $response['jsonapi']);
        $this->assertEquals($hash, sha1($timestamp . json_encode($response)));
    }

    public function seeResponseContainsJsonKey(string $key = 'data', array $data = []): void
    {
        $this->seeResponseContainsJson([$key => $data]);
    }

    public function seeResponseIsValidDeleteJson(): void
    {
        $this->seeResponseCodeIs(HttpCode::NO_CONTENT);
        $this->seeResponseEquals('');
    }

    public function seeResponseIsValidDeleteJsonApi(): void
    {
        $this->seeResponseIsValidDeleteJson();
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
