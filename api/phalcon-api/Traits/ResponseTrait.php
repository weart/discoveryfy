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

namespace Phalcon\Api\Traits;

use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Mvc\Micro;

/**
 * Trait ResponseTrait
 */
trait ResponseTrait
{
    /**
     * Halt execution after setting the error in the response
     *
     * @param Micro  $api
     * @param int    $httpCode
     * @param string $title
     * @return false
     */
    protected function halt(Micro $api, int $httpCode, string $title)
    {
        /** @var Response $response */
        $response = $api->getService('response');

        /** @var Request $request */
        $request = $api->getService('request');

        $response->sendApiError($request->getContentType(), $httpCode, $title);

        $api->stop();

        return false;
    }
}
