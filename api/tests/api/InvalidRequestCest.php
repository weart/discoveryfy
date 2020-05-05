<?php

namespace Discoveryfy\Tests\api;

use Codeception\Util\HttpCode;
use ApiTester;
use Page\Data;

class InvalidRequestCest
{
    public function invalidPostJson(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST(Data::$wrongUrl, '{"a":t}');
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Syntax error');
    }

    public function invalidPostJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->sendPOST(Data::$wrongUrl, '{"a":t}');
        $I->seeResponseIsJsonApiError(HttpCode::BAD_REQUEST, 'Syntax error');
    }

    public function invalidChineseURLJson(ApiTester $I)
    {
        // Valid chars:
        // ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~:/?#[]@!$&'()*+,;=
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('accept', 'application/json');
        $I->sendGET('%E5%85%B3%E4%BA%8E%E4%B8%AD%E6%96%87%E7%BB%B4%E5%9F%BA%E7%99%BE%E7%A7%91'); //Chinese
        $I->seeResponseIsJsonError(HttpCode::NOT_FOUND);
    }

    public function invalidChineseURLJsonApi(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/vnd.api+json');
        $I->haveHttpHeader('accept', 'application/vnd.api+json');
        $I->sendGET('%E5%85%B3%E4%BA%8E%E4%B8%AD%E6%96%87%E7%BB%B4%E5%9F%BA%E7%99%BE%E7%A7%91'); //Chinese
        $I->seeResponseIsJsonApiError(HttpCode::NOT_FOUND);
    }
}
