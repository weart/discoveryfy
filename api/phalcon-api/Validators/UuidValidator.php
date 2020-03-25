<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Validators;

use Phalcon\Validation\Validator\Regex;

class UuidValidator extends Regex
{
    public function __construct(array $options = array())
    {
        parent::__construct(array_merge([
            'pattern' => '/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD',
            'allowEmpty' => false,
            'message' => 'Invalid uuid'
        ], $options));
    }
}
