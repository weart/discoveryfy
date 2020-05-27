<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Transformers;

use Discoveryfy\Exceptions\ModelException;
use League\Fractal\TransformerAbstract;

/**
 * Class SearchTransformer
 *
 * @see https://fractal.thephpleague.com/transformers/
 * @package Discoveryfy\Transformers
 */
class SearchTransformer extends TransformerAbstract
{
    /**
     * @param array $model
     *
     * @return array
     * @throws ModelException
     */
    public function transform(array $model)
    {
        return $model;
    }
}
