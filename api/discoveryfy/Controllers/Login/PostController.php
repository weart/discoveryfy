<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Login;

use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\NotImplementedException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
//use Discoveryfy\Models\Users;
//use Discoveryfy\Models\Sessions;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * If username & password is provided, try to authenticate an user to the api.
 * Otherwise a anon session will be created.
 *
 * Module       Login
 * Class        PostController
 * OperationId  session.create
 *
 * @see https://github.com/phalcon/vokuro/blob/4.0.x/src/Controllers/UsersController.php
 * @see https://github.com/phalcon/vokuro/blob/4.0.x/src/Controllers/SessionController.php
 * @see https://github.com/phalcon/vokuro/blob/4.0.x/src/Forms/LoginForm.php
 * @property Cache        $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends Controller
{
    public function callAction()
    {
        //Check CSRF token (move this to Auth?)
        if (!$this->request->hasHeader('X-CSRF-TOKEN')) {
            throw new UnauthorizedException('CSRF not provided');
        }

        if (!$this->cache->has(CacheKeys::getLoginCSRFCacheKey($this->request->getHeader('X-CSRF-TOKEN')))) {
            throw new BadRequestException('Invalid CSRF token');
        }
        //Delete CSRF token?

        //Check credentials: username & password are optional
        if (!empty($this->request->getPost())) {
            if ($this->request->hasPost('username') && $this->request->hasPost('password')) {
                $this->auth->check([
                    'username' => $this->request->getPost('username', Filter::FILTER_STRING),
                    'password' => $this->request->getPost('password', Filter::FILTER_STRING)
                ]);

            } else if ($this->request->hasPost('username')) {
                throw new NotImplementedException('Recover password');
//                $this->auth->recoverPassword($this->request->hasPost('username'));

            } else {
                throw new BadRequestException();
            }
        }

        //Create Session & JWT token
        $token = $this->auth->createSessionToken();

        //@ToDo: Improve this with some kind of automatic schema generator
        $attrs = [
            'jwt' => $token,
            'session' => [ //@ToDo: $this->auth->getSession()->dump() or ->toArray()
                'id' => $this->auth->getSession()->get('id'),
//                'created-at' => $this->auth->getSession()->get('created_at'),
                'created-at' => $this->auth->getSession()->getCreatedAt(),
                'name' => $this->auth->getSession()->get('name'),
            ]
        ];
        if ($this->auth->getUser()) { //Saved in auth, otherwise getRelated('user') can be used
            $attrs += [
                'user' => [ //@ToDo: $this->auth->getSession()->dump() or ->toArray()
                    'id' => $this->auth->getUser()->get('id'),
                ]
            ];
        }

        switch ($this->request->getContentType()) {
            case 'application/vnd.api+json':
                $this->response->setJsonApiContent([
                    'type' => 'Login.Response',
                    'attributes' => $attrs
                ])->sendJsonApi();
                break;

            case 'application/ld+json':
                $this->response->setJsonContent([
                    '@context' => 'string',
                    '@id' => 'string',
                    '@type' => 'string',
                    'jwt' => $token
                ])->sendJsonLd();
                break;

            case 'application/json':
            default:
                $this->response->setJsonContent($attrs)->send();
                break;
        }
    }
}
