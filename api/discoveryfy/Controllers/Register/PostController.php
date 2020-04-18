<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Register;

//use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
//use Discoveryfy\Exceptions\NotImplementedException;
//use Discoveryfy\Exceptions\UnauthorizedException;
//use Discoveryfy\Models\SecurityEvents;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
//use Phalcon\Api\Traits\QueryTrait;
//use Phalcon\Api\Traits\ResponseTrait;
//use Phalcon\Api\Transformers\BaseTransformer;
//use Phalcon\Cache;
//use Phalcon\Config;
use Phalcon\Filter;
//use Phalcon\Mvc\Controller;
use Phalcon\Http\ResponseInterface;
use Phalcon\Security\Random;

/**
 * Create one new user in the platform
 *
 * Module       Register
 * Class        PostController
 * OperationId  user.create
 * Operation    POST
 * OperationUrl /register
 * Security     Request must have a CSRF token
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends BaseItemApiController
{
    use FractalTrait;
//    use QueryTrait;
//    use ResponseTrait;

    /** @var string */
//    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USER;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    public function checkSecurity($parameters): array
    {
        $this->auth->checkCSRFRegister();
        return $parameters;
    }

    public function coreAction(): ResponseInterface
    {
        if (empty($this->request->getPost())) {
            throw new BadRequestException('Empty post');
        }
        // @ToDo: Move this into model validation
        if ($this->request->getPost('username') !== $this->request->getPost('username', Filter::FILTER_STRIPTAGS)) {
            throw new BadRequestException('Invalid username');
        }
        if ($this->request->getPost('password') !== $this->request->getPost('password', Filter::FILTER_STRING)) {
            throw new BadRequestException('Invalid password');
        }

        $user = new Users();
        $user
            ->set('id', (new Random())->uuid())
            ->set('username', $this->request->getPost('username', Filter::FILTER_STRIPTAGS))
//            ->set('password', $this->security->hash($this->request->getPost('password', Filter::FILTER_STRING)))
            ->setPasswordHash($this->security->hash($this->request->getPost('password', Filter::FILTER_STRING)))
            ->set('email', $this->request->getPost('email', Filter::FILTER_EMAIL))
            ->set('enabled', $this->request->getPost('enabled', Filter::FILTER_BOOL, true))
            ->set('public_visibility', $this->request->getPost('public_visibility', Filter::FILTER_BOOL, false))
            ->set('public_email', $this->request->getPost('public_email', Filter::FILTER_BOOL, false))
            ->set('language', $this->request->getPost('language', Filter::FILTER_STRIPTAGS, 'en'))
            ->set('theme', $this->request->getPost('theme', Filter::FILTER_STRIPTAGS, 'default'))
//            ->set('rol', $this->request->getPost('rol', Filter::FILTER_STRIPTAGS));
            ->set('rol', 'ROLE_USER');

        if (true !== $user->validation() || true !== $user->save()) {
            if (false === $user->validationHasFailed()) {
                throw new InternalServerErrorException('Error creating user');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $user->getMessages());
        }

        //@ToDo: Send mail with token, & create event when mail is confirmed
//        (new SecurityEvents())->createEmailConfirmationEvent($this->request, $user);

//        return $this->sendApiData($user);
        return $this->response->sendApiContentCreated(
            $this->request->getContentType(),
            $this->format($this->method, $user, $this->transformer, $this->resource)
        );
    }
}
