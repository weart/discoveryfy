<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Controllers;

use Discoveryfy\Exceptions\BadRequestException;
use Phalcon\Api\Filters\UUIDFilter;
use Phalcon\Filter;
use Phalcon\Mvc\Controller;

/**
 * Class BaseController
 *
 * @see https://docs.phalcon.io/4.0/en/controllers
 * @property Filter              $filter
 */
abstract class BaseController extends Controller
{
    /**
     * Checks the passed id parameter and returns the relevant array back
     *
     * @param string $recordId
     * @return string
     * @throws BadRequestException
     */
    protected function checkId(string $recordId = ''): string
    {
        if (false === ($id = $this->filter->sanitize($recordId, UUIDFilter::FILTER_NAME))) {
            throw new BadRequestException('Invalid uuid');
        }
        return $id;
    }
}
