<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Login;

use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Mvc\Controller;

/**
 * Get one valid CSRF token
 *
 * Module       Login
 * Class        GetController
 * OperationId  session.create.csrf
 *
 * @property Auth         $auth
 * @property Request      $request
 * @property Response     $response
 */
class GetController extends Controller
{
    public function callAction()
    {
        $token = $this->auth->createCSRFLogin();

        return $this->response->sendApiContent(
            $this->request->getContentType(),
            [
                'type' => 'CSRF',
                'id' => $token
            ]
        );
    }
}
