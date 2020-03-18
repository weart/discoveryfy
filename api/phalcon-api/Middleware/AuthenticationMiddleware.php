<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Middleware;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\Auth;
use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Config;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class AuthenticationMiddleware
 *
 * @package Phalcon\Api\Middleware
 */
class AuthenticationMiddleware implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * Verify JWT Token when the page is not public
     *
     * @param Micro $api
     *
     * @return bool
     * @throws InternalServerErrorException
     */
    public function call(Micro $api)
    {
        /** @var Request $request */
        $request  = $api->getService('request');
        /** @var Response $response */
        $response = $api->getService('response');
        /** @var Config $config */
        $config   = $api->getService('config');
        /** @var Auth $auth */
        $auth     = $api->getService('auth');

        if ($this->isPublicPage($config, $request->getURI())) {
            return true;
        }

        if ($request->isEmptyBearerToken()) {
            return $this->halt(
                $api,
                $response::OK,
                'Invalid Token'
            );
        }
        if (true !== $auth->verifyToken($request->getBearerTokenFromHeader())) {
            return $this->halt(
                $api,
                $response::OK,
                'Invalid Token'
            );
        }

        return true;
    }

    /**
     * @param Config $config
     * @param string $uri
     * @return bool
     * @throws InternalServerErrorException
     */
    private function isPublicPage(Config $config, string $uri): bool
    {
        if (!$config->has('public_routes')) {
            throw new InternalServerErrorException('App without any public route');
        }
        return in_array($uri, $config->get('public_routes')->toArray(), true);
    }
}
