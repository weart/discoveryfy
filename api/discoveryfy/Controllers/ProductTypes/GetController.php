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

namespace Discoveryfy\Controllers\ProductTypes;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Models\ProductTypes;
use Discoveryfy\Transformers\ProductTypesTransformer;
use Phalcon\Api\Controllers\BaseController;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = ProductTypes::class;

    /** @var array */
    protected $includes    = [
        Relationships::PRODUCTS,
    ];

    /** @var string */
    protected $resource    = Relationships::PRODUCT_TYPES;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'          => true,
        'name'        => true,
        'description' => false,
    ];

    /** @var string */
    protected $transformer = ProductTypesTransformer::class;
}
