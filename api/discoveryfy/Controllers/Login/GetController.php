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
use Discoveryfy\Exceptions\InternalServerErrorException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Mvc\Controller;
use Phalcon\Security;

/**
 * Get one valid CSRF token
 *
 * Module       Login
 * Class        GetController
 * OperationId  session.create.csrf
 *
 * @property Cache        $cache
 * @property Security     $security
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class GetController extends Controller
{
    public function callAction()
    {
        //Create CSRF token (move this to Auth?)
        $token = $this->security->getToken();
        if (true !== $this->cache->set(CacheKeys::getLoginCSRFCacheKey($token), null)) {
            throw new InternalServerErrorException('Problem saving token into cache');
        }

        switch ($this->request->getContentType()) {
            case 'application/vnd.api+json':
                $this->response->setJsonApiContent([
                    'type' => 'CSRF',
                    'id' => $token
                ])->sendJsonApi();
                break;

            case 'application/ld+json':
                $this->response->setJsonContent([
                    '@context' => 'string',
                    '@id' => 'string',
                    '@type' => 'string',
                    'csrf' => $token
                ])->sendJsonLd();
                break;

            case 'application/json':
            default:
                $this->response->setJsonContent($token)->send();
                break;
        }
    }
}
