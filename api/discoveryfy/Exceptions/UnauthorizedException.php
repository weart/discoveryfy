<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Exceptions;

class UnauthorizedException extends Exception
{
    public $http_code = 401;
    public $http_msg = 'The request requires user authentication.';
}
