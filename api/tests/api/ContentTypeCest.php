<?php

namespace Discoveryfy\Tests\api;

use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;

class ContentTypeCest
{
    private $not_found_msg = '404 (Not Found)';

    public function wrongUrlAddCharsetJson(Login $I)
    {
        $I->setContentType('application/json');
        $I->sendGET(Data::$wrongUrl);
        $I->seeHttpHeader('Content-Type', 'application/json; charset=UTF-8');
        $I->seeResponseIsJsonError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function wrongUrlAddCharsetJsonApi(Login $I)
    {
        $I->setContentType('application/vnd.api+json');
        $I->sendGET(Data::$wrongUrl);
        $I->seeHttpHeader('Content-Type', 'application/vnd.api+json; charset=UTF-8');
        $I->seeResponseIsJsonApiError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function wrongUrlDefaultContentType(Login $I)
    {
        $I->sendGET(Data::$wrongUrl);
        $I->seeHttpHeader('Content-Type', 'application/json; charset=UTF-8');
        $I->seeResponseIsJsonError(HttpCode::NOT_FOUND, $this->not_found_msg);
    }

    public function wrongUrlWrongContentType(Login $I)
    {
        $I->setContentType('sommething');
        $I->sendGET(Data::$wrongUrl);
        $I->seeHttpHeader('Content-Type', 'application/json; charset=UTF-8');
        $I->seeResponseIsJsonError(HttpCode::BAD_REQUEST, 'Invalid content type');
    }
}
