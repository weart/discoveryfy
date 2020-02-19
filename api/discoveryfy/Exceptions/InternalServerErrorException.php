<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Exceptions;

class InternalServerErrorException extends Exception
{
    public $http_code = 500;
    public $http_msg = 'The server encountered an unexpected condition which prevented it from fulfilling the request.';
}
