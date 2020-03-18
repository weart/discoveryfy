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

namespace Phalcon\Api\Providers;

use Phalcon\Api\Http\Response as ApiResponse;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class ResponseProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared('response', function () {
            $res = new ApiResponse();
            //Define default status code
            if (is_null($res->getStatusCode())) {
                $res->setStatusCode(ApiResponse::OK);
            }
            //Define default content type?
            return $res;
        });
    }
}
