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
use Discoveryfy\Models\Companies;
use League\Fractal\Resource\Collection;
use Phalcon\Api\Transformers\BaseTransformer;

/**
 * Class CompaniesTransformer
 */
class CompaniesTransformer extends BaseTransformer
{
    protected $availableIncludes = [
        Relationships::PRODUCTS,
        Relationships::INDIVIDUALS,
    ];

    /**
     * @param Companies $company
     *
     * @return Collection
     */
    public function includeIndividuals(Companies $company)
    {
        return $this->getRelatedData(
            'collection',
            $company,
            IndividualsTransformer::class,
            Relationships::INDIVIDUALS
        );
    }

    /**
     * @param Companies $company
     *
     * @return Collection
     */
    public function includeProducts(Companies $company)
    {
        return $this->getRelatedData(
            'collection',
            $company,
            ProductsTransformer::class,
            Relationships::PRODUCTS
        );
    }
}
