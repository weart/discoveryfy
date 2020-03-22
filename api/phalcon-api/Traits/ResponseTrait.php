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

use Phalcon\Api\Http\Response;
use Phalcon\Mvc\Micro;

/**
 * Trait ResponseTrait
 */
trait ResponseTrait
{
    /**
     * Halt execution after setting the message in the response
     *
     * @param Micro  $api
     * @param int    $status
     * @param string $message
     *
     * @return false
     */
    protected function halt(Micro $api, int $status, string $message)
    {
        /** @var Response $response */
        $response = $api->getService('response');
        $response
            ->setPayloadError($status, $message)
            ->setStatusCode($status);

        switch ($api->getService('request')->getContentType()) {
            case 'application/vnd.api+json':
                $response->sendJsonApi();
                break;

            case 'application/ld+json':
                $response->sendJsonLd();
                break;

            case 'application/json':
            default:
                $response->send();
                break;
        }

        $api->stop();

        return false;
    }
}
