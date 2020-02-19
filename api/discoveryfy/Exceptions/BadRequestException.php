<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Exceptions;

class BadRequestException extends Exception
{
    public $http_code = 400;
    public $http_msg = 'The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications.';
}
