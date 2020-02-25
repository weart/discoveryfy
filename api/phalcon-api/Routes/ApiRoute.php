<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Routes;

//use Phalcon\Api\Routes\RouteInterface;

class ApiRoute implements RouteInterface
{
    private $controller_class;
    private $controller_method;
    private $http_route;
    private $http_method;

    public function __construct($controller_class, $http_route = '/', $http_method = 'get', $controller_method = 'callAction')
    {
        $this->controller_class = $controller_class;
        $this->controller_method = $controller_method;
        $this->http_route = $http_route;
        $this->http_method = $http_method;
    }

    public function getControllerClass(): string
    {
        return $this->controller_class;
    }

    public function getControllerMethod(): string
    {
        return $this->controller_method;
    }

    public function getHttpRoute(): string
    {
        return $this->http_route;
    }

    public function getHttpMethod(): string
    {
        return $this->http_method;
    }
}
