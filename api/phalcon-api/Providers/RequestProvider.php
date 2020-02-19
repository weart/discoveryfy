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

use Phalcon\Api\Http\Request;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Di\DiInterface;

class RequestProvider implements ServiceProviderInterface
{
    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $req = new Request();
        //Check if Content-Type has the charset appended like "application/json;charset=utf-8"?
        $valid_content_type = [
            'application/json',
            'application/vnd.api+json',
            'application/ld+json'
        ];
        if (in_array($req->getHeader('Content-Type'), $valid_content_type, true)) {
            //Input not sanitized! Must be done in each param
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $container->setShared('request', $req);
    }
}
