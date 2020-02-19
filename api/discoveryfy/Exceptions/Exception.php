<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Exceptions;

use Phalcon\Exception as PhException;

abstract class Exception extends PhException
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (empty($message) && isset($this->http_msg)) {
            $message = $this->http_msg;
        }
        if (empty($code) && isset($this->http_code)) {
            $code = $this->http_code;
        }
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return "Error {$this->http_code}: {$this->http_msg}";
    }

    public function toJson()
    {
        return [
            'status' => $this->http_code,
            'title' => $this->http_msg
        ];
    }
}
