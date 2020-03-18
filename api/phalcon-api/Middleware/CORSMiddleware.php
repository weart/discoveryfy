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

namespace Phalcon\Api\Middleware;

use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Events\Event;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class CORSMiddleware
 * Add ORIGIN header for CORS
 *
 * @ToDo: In RouterProver uncomment this middleware
 *
 * @see https://www.html5rocks.com/en/tutorials/cors/
 * @property Request  $request
 * @property Response $response
 */
class CORSMiddleware implements MiddlewareInterface
{
    use ResponseTrait;

    /**
     * @param Event $event
     * @param Micro $application
     *
     * @returns bool
     */
    public function beforeHandleRoute(
        Event $event,
        Micro $application
    ) {

        if ($application->request->getHeader('ORIGIN')) {
            $origin = $application
                ->request
                ->getHeader('ORIGIN')
            ;
        } else {
            $origin = '*';
        }

        $application
            ->response
            ->setHeader(
                'Access-Control-Allow-Origin',
                $origin
            )
            ->setHeader(
                'Access-Control-Allow-Methods',
                'GET,PUT,POST,DELETE,OPTIONS'
            )
            ->setHeader(
                'Access-Control-Allow-Headers', //'Access-Control-Request-Headers'
                'Origin, Content-Type, Authorization' //'X-Requested-With, Content-Range, Content-Disposition,'
            )
            // By default, cookies are not included in CORS requests.
            // Use this header to indicate that cookies should be included in CORS requests. The only valid value for this header is true (all lowercase). If you don't need cookies, don't include this header (rather than setting its value to false).
//            ->setHeader(
//                'Access-Control-Allow-Credentials',
//                'true'
//            )
        ;

        //Stop request if METHOD is OPTIONS
        if ($application->request->isOptions()) {
            return $this->halt(
                $application,
                $application->response::OK,
                $application->response->getHttpCodeDescription($application->response::OK)
            );
        }
    }

    /**
     * @param Micro $application
     *
     * @return true
     */
    public function call(Micro $application): bool
    {
        return true;
    }
}
