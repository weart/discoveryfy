<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers;

use Discoveryfy\Exceptions\ModelException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Discoveryfy\Models\Users;
use Phalcon\Api\Traits\QueryTrait;
use Phalcon\Api\Traits\TokenTrait;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class LoginController
 *
 * @property Cache $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class LoginController extends Controller
{
    use TokenTrait;
    use QueryTrait;

    /**
     * Default action logging in
     *
     * @return array
     * @throws ModelException
     */
    public function callAction()
    {
        $username = $this->request->getPost('username', Filter::FILTER_STRING);
        $password = $this->request->getPost('password', Filter::FILTER_STRING);
        $filter = new Filter();
        $json = json_decode($this->request->getRawBody(),true);
//        $json = $this->request->getJsonRawBody(true);
        if (!$username && isset($json['username'])) {
            $username = $filter->sanitize( $json['username'], 'string');
        }
        if (!$password && isset($json['password'])) {
            $password = $filter->sanitize( $json['password'], 'string');
        }
        /** @var Users|false $user */
        $user     = $this->getUserByUsernameAndPassword($this->config, $this->cache, $username, $password);

        if (false !== $user) {
            $this
                ->response
                ->setPayloadSuccess(['token' => $user->getToken()]);
        } else {
            $this
                ->response
                ->setPayloadError('Incorrect credentials')
            ;
        }
    }
}
