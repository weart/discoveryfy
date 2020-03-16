<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Register;

use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\ModelException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\SecurityEvents;
use Discoveryfy\Transformers\UserTransformer;
use Discoveryfy\Validators\UserValidator;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Discoveryfy\Models\Users;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;
use Phalcon\Security\Random;

/**
 * Create one new user in the platform
 *
 * Module       Register
 * Class        PostController
 * OperationId     user.create
 *
 * @property Cache        $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends Controller
{
    use QueryTrait;
    use FractalTrait;
    use ResponseTrait;

    public function callAction()
    {
        //Check CSRF token (move this to Auth?)
        if (!$this->request->hasHeader('X-CSRF-TOKEN')) {
            throw new UnauthorizedException('CSRF not provided');
        }

        if (!$this->cache->has(CacheKeys::REGISTER_CSRF . $this->request->getHeader('X-CSRF-TOKEN'))) {
            throw new UnauthorizedException('Invalid CSRF token');
        }
        //Delete CSRF token?

        if (empty($this->request->getPost())) {
            throw new BadRequestException('Empty post');
        }

        //create new user
//        $messages  = (new UserValidator())->validate($this->request->getPost());
//        if (count($messages) > 0) {
////            $this
////                ->response
////                ->setPayloadErrors($messages);
//            throw new BadRequestException('prevalidation');
//        }

        //@ToDo: Validation is separated class or inside the model?
        $user = new Users();
        if ($this->request->getPost('username') !== $this->request->getPost('username', Filter::FILTER_STRIPTAGS)) {
            throw new BadRequestException('Invalid username');
        }
        if ($this->request->getPost('password') !== $this->request->getPost('password', Filter::FILTER_STRING)) {
            throw new BadRequestException('Invalid password');
        }
        $user
            ->set('id', (new Random())->uuid())
            ->set('username', $this->request->getPost('username', Filter::FILTER_STRIPTAGS))
//            ->set('password', $this->security->hash($this->request->getPost('password', Filter::FILTER_STRING)))
            ->setPasswordHash($this->security->hash($this->request->getPost('password', Filter::FILTER_STRING)))
            ->set('email', $this->request->getPost('email', Filter::FILTER_EMAIL))
            ->set('enabled', $this->request->getPost('enabled', Filter::FILTER_BOOL, true))
            ->set('publicVisibility', $this->request->getPost('public-visibility', Filter::FILTER_BOOL, false))
            ->set('publicEmail', $this->request->getPost('public-email', Filter::FILTER_BOOL, false))
            ->set('language', $this->request->getPost('language', Filter::FILTER_STRIPTAGS, 'en'))
            ->set('theme', $this->request->getPost('theme', Filter::FILTER_STRIPTAGS, 'default'))
//            ->set('rol', $this->request->getPost('rol', Filter::FILTER_STRIPTAGS));
            ->set('rol', 'ROLE_USER');

        if(true !== $user->validation()) {
            throw new BadRequestException($user->getMessage());
//            throw new BadRequestException('Invalid postvalidation');
        }

//        $user = (new Users())->assign([
//            'id'                => (new Random())->uuid(),
//            'username'          => $this->request->getPost('username', 'alpha'),
//            'password'          => $this->security->hash($this->request->getPost('password', 'striptags')),
//            'email'             => $this->request->getPost('email', 'email'),
//            'enabled'           => $this->request->getPost('enabled', 'bool'),
//            'publicVisibility'  => $this->request->getPost('publicVisibility', 'bool'),
//            'publicEmail'       => $this->request->getPost('publicEmail', 'bool'),
//            'language'          => $this->request->getPost('language', 'striptags'),
//            'theme'             => $this->request->getPost('theme', 'striptags'),
//            'rol'               => $this->request->getPost('rol', 'striptags'),
//        ]);

        if (false === $user->save()) {
            throw new InternalServerErrorException($user->getMessage());
//            throw new InternalServerErrorException('Error creating user');
        }

        //@ToDo: Send mail with token, & create event when mail is confirmed
//        (new SecurityEvents())->createEmailConfirmationEvent($this->request, $user);

        //@ToDo: Improve this with some kind of automatic schema generator
        $schema = $this->format(
            'item',
            $user,
            UserTransformer::class, //or BaseTransformer::class?
            Users::class
//            $related,
//            $fields
        );

        $this->response->setStatusCode($this->response::CREATED);

        switch ($this->request->getContentType()) {
            case 'application/vnd.api+json':
                $this->response->setJsonApiContent([
                    'type' => 'User.Read',
                    'attributes' => $schema
                ])->sendJsonApi();
                break;

            case 'application/ld+json':
                $this->response->setJsonContent([
                    '@context' => 'string',
                    '@id' => 'string',
                    '@type' => 'string',
                    'User.Read' => $schema
                ])->sendJsonLd();
                break;

            case 'application/json':
            default:
                $this->response->setJsonContent($schema)->send();
                break;
        }
    }
}
