<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Routes;

interface RouteInterface
{
    public function getControllerClass(): string;
    public function getControllerMethod(): string;
    public function getHttpRoute(): string;
    public function getHttpMethod(): string;
}
