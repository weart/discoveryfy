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

namespace Phalcon\Api\Controllers;

//use Discoveryfy\Exceptions\BadRequestException;
//use Phalcon\Api\Filters\UUIDFilter;
use Discoveryfy\Exceptions\BadRequestException;
use Phalcon\Api\Http\Response;
//use Phalcon\Api\Traits\FractalTrait;
//use Phalcon\Api\Traits\QueryTrait;
//use Phalcon\Api\Traits\ResponseTrait;
//use Phalcon\Cache;
//use Phalcon\Config;
use Phalcon\Filter;
//use Phalcon\Http\ResponseInterface;
//use Phalcon\Mvc\Controller;
//use Phalcon\Mvc\Micro;
//use Phalcon\Mvc\Model\MetaData\Libmemcached as ModelsMetadataCache;
use function explode;
use function implode;
//use function in_array;
use function strtolower;
use function substr;

/**
 * Class BaseCollectionApiController
 *
 * @see https://docs.phalcon.io/4.0/en/controllers
 * @property Response            $response
 * #property Cache               $cache
 * #property Config              $config
 * #property Micro               $application
 * #property ModelsMetadataCache $modelsMetadata
 */
abstract class BaseCollectionApiController extends BaseItemApiController
{
//    use FractalTrait;
//    use QueryTrait;
//    use ResponseTrait;

    /** @var string */
    protected $method = 'collection'; //Valid methods: item or collection

    /** @var string */
//    protected $orderBy = 'name';
    protected $orderBy = '';

    /** @var array */
    protected $sortFields = [];

    protected function findRecords(array $parameters = [])
    {
        $validSort  = $this->checkSort();
        if (true !== $validSort) {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::BAD_REQUEST);
        }

        $results = $this->getRecords($parameters, $this->orderBy, $this->getPagination());
        if (count($parameters) > 0 && 0 === $results->count()) {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::NOT_FOUND);
        }
        return $results;
    }

    /**
     * Process the sort. If supplied change the `orderBy` of the builder. If a
     * field that is not supported has been supplied return false
     *
     * @return bool
     */
    protected function checkSort(): bool
    {
        $sortArray  = [];
        $sortFields = $this->request->getQuery('sort', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');
        if (true !== empty($sortFields)) {
            $requestedSort = explode(',', $sortFields);
            foreach ($requestedSort as $field) {
                list($trueField, $direction) = $this->getFieldAndDirection($field);
                /**
                 * Is this a valid field and is it sortable? If yes, process it
                 */
                if (true === ($this->sortFields[$trueField] ?? false)) {
                    $sortArray[] = $trueField . $direction;
                } else {
                    return false;
                }
            }
        }

        /**
         * Check the results. If we have something update the $orderBy
         */
        if (count($sortArray) > 0) {
            $this->orderBy = implode(',', $sortArray);
        }

        return true;
    }

    /**
     * Return the field name and direction
     *
     * @param string $field
     * @return array
     */
    protected function getFieldAndDirection(string $field): array
    {
        $trueField = strtolower($field);
        $direction = ' asc';

        /**
         * Ascending or descending
         */
        if ('-' === substr($trueField, 0, 1)) {
            $trueField = substr($trueField, 1);
            $direction = ' desc';
        }

        return [$trueField, $direction];
    }

    protected function getPagination(): array
    {
        $page = (int) $this->request->getQuery('page', Filter::FILTER_ABSINT, 1);
        $limit = (int) $this->request->getQuery('limit', Filter::FILTER_ABSINT, 10);
        if ($limit < 1 || $limit > 100) {
            throw new BadRequestException('Invalid limit');
        }
        $offset = ($page-1) * $limit;
        return [ 'page' => $page, 'limit' => $limit, 'offset' => $offset ];
    }
}
