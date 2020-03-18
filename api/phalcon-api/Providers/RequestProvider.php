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

//use Phalcon\Api\Filters\UUIDFilter;
use Discoveryfy\Exceptions\BadRequestException;
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
//        if (in_array($req->getContentType(), $valid_content_type, true)) {
        if (in_array($req->getHeader('Content-Type'), $valid_content_type, true)) {
            if ($req->isPost()) {
                //Input not sanitized! Must be done in each param
                $_POST = json_decode(file_get_contents('php://input'), true);

                if (JSON_ERROR_NONE !== json_last_error()) {
                    throw new BadRequestException(json_last_error_msg());
                }
            }
        }

        //Define default filters?
//        $req->setParameterFilters('id', UUIDFilter::FILTER_NAME);
//        $req->getFilteredQuery('id') $req->getFilteredPost('id')

        $container->setShared('request', $req);
    }
}
