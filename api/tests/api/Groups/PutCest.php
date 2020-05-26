<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Groups;

use Codeception\Exception\TestRuntimeException;
use Codeception\Util\HttpCode;
use Page\Data;
use Phalcon\Security\Random;
use Step\Api\Login;

class GroupsPutCest
{
    private $unauthorized_msg = 'Only available when the group has public_visibility or you belong to the group';

    public function modifyGroupJson(Login $I): void
    {
        $this->modifyGroup($I, 'application/json');
    }

    public function modifyGroupJsonApi(Login $I): void
    {
        $this->modifyGroup($I, 'application/vnd.api+json');
    }

    private function modifyGroup(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['name'] = 'test_'.(new Random())->hex(5);
        $modified_attrs['description'] = 'test_'.(new Random())->hex(5);

        // Change group name and description
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'name' => $modified_attrs['name'],
            'description' => $modified_attrs['description']
        ]);
        $this->seeResponseIsGroupSuccessful($I, $modified_attrs, $contentType);

        //Leave group as before
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), $previous_attrs);
        $this->seeResponseIsGroupSuccessful($I, $previous_attrs, $contentType);
    }

    public function modifyGroupVisibilityJson(Login $I): void
    {
        $this->modifyGroupVisibility($I, 'application/json');
    }

    public function modifyGroupVisibilityJsonApi(Login $I): void
    {
        $this->modifyGroupVisibility($I, 'application/vnd.api+json');
    }

    private function modifyGroupVisibility(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        list($anon_jwt, $anon_session_id, $anon_user_id) = $I->loginAsAnon();
        list($alt_jwt, $alt_session_id, $alt_user_id) = $I->loginAsSecondaryTest();
        $previous_attrs = $modified_attrs = $I->getKnownGroupAttributes();
        $modified_attrs['public_visibility'] = true;

        // Group is not visible by anon and another user
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$alt_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $I->seeResponseIsJsonError(HttpCode::UNAUTHORIZED, $this->unauthorized_msg);

        // Group is only visible to members
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $this->seeResponseIsGroupSuccessful($I, $previous_attrs, $contentType);

        // Make group visible to everyone
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'public_visibility' => $modified_attrs['public_visibility']
        ]);
        $this->seeResponseIsGroupSuccessful($I, $modified_attrs, $contentType);

        // Everybody can see the group
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$anon_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $this->seeResponseIsGroupSuccessful($I, $modified_attrs, $contentType);

        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$alt_jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $this->seeResponseIsGroupSuccessful($I, $modified_attrs, $contentType);

        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendGET(sprintf(Data::$groupUrl, $previous_attrs['id']));
        $this->seeResponseIsGroupSuccessful($I, $modified_attrs, $contentType);

        // Leave group to private
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPUT(sprintf(Data::$groupUrl, $previous_attrs['id']), [
            'public_visibility' => $previous_attrs['public_visibility']
        ]);
        $this->seeResponseIsGroupSuccessful($I, $previous_attrs, $contentType);
    }

    private function seeResponseIsGroupSuccessful(Login $I, array $attrs, string $contentType): void
    {
        if ($contentType === 'application/json') {
            $this->seeResponseIsGroupJsonSuccessful($I, $attrs);

        } else if ($contentType === 'application/vnd.api+json') {
            $this->seeResponseIsGroupJsonApiSuccessful($I, $attrs);

        } else {
            throw new TestRuntimeException('Invalid contentType');
        }
    }

    private function seeResponseIsGroupJsonSuccessful(Login $I, array $attrs): void
    {
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::groupResponseJsonType(), [
            'type'                              => 'groups',
            'attributes.name'                   => $attrs['name'],
            'attributes.description'            => $attrs['description'],
            'attributes.public_visibility'      => $attrs['public_visibility'],
            'attributes.public_membership'      => $attrs['public_membership'],
            'attributes.who_can_create_polls'   => $attrs['who_can_create_polls']
        ]);
    }

    private function seeResponseIsGroupJsonApiSuccessful(Login $I, array $attrs): void
    {
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::groupResponseJsonApiType(), [
            'type'                          => 'groups',
            'attributes' => [
                'name'                      => $attrs['name'],
                'description'               => $attrs['description'],
                'public_visibility'         => $attrs['public_visibility'],
                'public_membership'         => $attrs['public_membership'],
                'who_can_create_polls'      => $attrs['who_can_create_polls'],
            ]
        ]);
    }
}
