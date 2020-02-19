<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Transformers;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Models\ProductTypes;
use League\Fractal\Resource\Collection;
use Phalcon\Api\Transformers\BaseTransformer;

/**
 * Class ProductTypesTransformer
 */
class ProductTypesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::PRODUCTS,
    ];

    /**
     * @param ProductTypes $type
     *
     * @return Collection
     */
    public function includeProducts(ProductTypes $type)
    {
        return $this->getRelatedData(
            'collection',
            $type,
            ProductsTransformer::class,
            Relationships::PRODUCTS
        );
    }
}
