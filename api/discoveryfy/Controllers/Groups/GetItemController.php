<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Groups;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\Memberships;
use Discoveryfy\Models\Organizations;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseController;
//use Phalcon\Api\Http\Request;
//use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Http\ResponseInterface;

/**
 * Retrieves a Group
 *
 * Module       Groups
 * Class        GetItemController
 * OperationId  group.get
 *
 * @property Auth         $auth
 * #property Request      $request
 * #property Response     $response
 */
class GetItemController extends BaseController
{
    use FractalTrait;

    /** @var string */
    protected $model       = Organizations::class;

    /** @var string */
    protected $resource    = Relationships::GROUP;

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /** @var string */
    protected $method = 'item';

    public function callAction(string $group_uuid = ''): ResponseInterface
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }

        // Any membership is valid
        $org = $this->checkUserMembership($group_uuid);

        // Reurn the object
//        return parent::callAction($group_uuid); //Avoid: the object can be reused and no includes or sorts are necessary
        return $this->response
            ->setStatusCode($this->response::OK)
            ->sendApiContent(
                $this->request->getContentType(),
                $this->format($this->method, $org, $this->transformer, $this->resource)
            );
    }

    private function checkUserMembership($group_uuid): Organizations
    {
        $rtn = $this->modelsManager->createBuilder()
            ->columns('org.*, member.rol as member_rol, user.id as user_id')
            ->from([ 'org' => Organizations::class])
            ->innerJoin(Memberships::class, 'org.id = member.organization_id', 'member')
            ->innerJoin(Users::class, 'member.user_id = user.id', 'user')
            ->where('org.id = :org_uuid: AND user.id = :user_uuid:')
            ->setBindTypes([ 'org_uuid' => \PDO::PARAM_STR, 'user_uuid' => \PDO::PARAM_STR ])
            ->setBindParams([ 'org_uuid' => $group_uuid, 'user_uuid' => $this->auth->getUser()->get('id') ])
            ->getQuery()->execute();

        if ($rtn->count() === 0) {
            throw new UnauthorizedException('Cannot modify this group');
        }
        if ($rtn->count() > 1) {
            throw new InternalServerErrorException('Only one membership should be possible');
        }
        $rtn = $rtn->getFirst();
        if ($rtn->org->get('id') !== $group_uuid || $rtn->user_id !== $this->auth->getUser()->get('id')) {
            throw new InternalServerErrorException('Strange error in the query');
        }
        // Any membership is valid
//        if (!in_array($rtn->member_rol, ['ROLE_ADMIN', 'ROLE_OWNER'])) {
//            throw new UnauthorizedException('Only admins and owners can delete a group');
//        }
        return $rtn->org;
    }
}
