<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Filters;

class UUIDFilter
{
    const FILTER_NAME = 'uuid';

    /**
     * Determine if a given string is a valid UUID.
     * @see Illuminate\Support\Str (Laravel framework)
     * @example 710cb0bd-1553-4119-8762-f565e687d50c is a valid uuid
     * @param  string $value
     * @return false|string
     */
    public function __invoke(string $value)
    {
        if (preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) !== 1) {
            return false;
        }

        return $value;
    }
}
