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
use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\NotImplementedException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
//use Discoveryfy\Models\Users;
//use Discoveryfy\Models\Sessions;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Api\Traits\FractalTrait;
use Phalcon\Api\Transformers\BaseTransformer;
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
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends Controller
{
    use FractalTrait;

    public function callAction()
    {
        $this->auth->checkCSRFLogin();

        //Check credentials: username & password are optional
        if (!empty($this->request->getPost())) {
            if ($this->request->hasPost('username') && $this->request->hasPost('password')) {
                $this->auth->check([
                    'username' => $this->request->getPost('username', Filter::FILTER_STRING),
                    'password' => $this->request->getPost('password', Filter::FILTER_STRING)
                ]);

            } elseif ($this->request->hasPost('username')) {
                throw new NotImplementedException('Recover password');
//                $this->auth->recoverPassword($this->request->hasPost('username'));
            } else {
                throw new BadRequestException();
            }
        }

        //Create Session & JWT token
        $token = $this->auth->createSessionToken();

        $response =
        [
            [
                'type' => 'jwt',
                'id'    => $token
            ],
            $this->format('item', $this->auth->getSession(), BaseTransformer::class, Relationships::SESSION)['data']
        ];
        if ($this->auth->getUser()) { //Saved in auth, otherwise getRelated('user') can be used
            $response[] = $this->format('item', $this->auth->getUser(), BaseTransformer::class, Relationships::USER)['data'];
        }

        return $this->response->sendApiContent($this->request->getContentType(), $response);
    }
}
