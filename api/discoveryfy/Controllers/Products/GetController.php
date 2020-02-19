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

namespace Discoveryfy\Controllers\Products;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Models\Products;
use Discoveryfy\Transformers\ProductsTransformer;
use Phalcon\Api\Controllers\BaseController;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Products::class;

    /** @var array */
    protected $includes    = [
        Relationships::COMPANIES,
        Relationships::PRODUCT_TYPES,
    ];

    /** @var string */
    protected $resource    = Relationships::PRODUCTS;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'          => true,
        'typeId'      => true,
        'name'        => true,
        'description' => false,
        'quantity'    => true,
        'price'       => true,
    ];

    /** @var string */
    protected $transformer = ProductsTransformer::class;
}
