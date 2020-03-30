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

namespace Discoveryfy\Controllers\Tests;

use Discoveryfy\Exceptions\ModelException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Discoveryfy\Models\Users;
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
class TestController extends Controller
{

    /**
     * Default action logging in
     *
     * @return array
     * @throws ModelException
     */
    public function callAction()
    {
        $test = $this->request->get('test', Filter::FILTER_BOOL);

        if (false !== $test) {
            return $this->response->sendApiContent($this->request->getContentType(), ['test' => 'ok']);
        } else {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::BAD_REQUEST, 'Incorrect credentials');
        }
    }
}
