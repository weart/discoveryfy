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
use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\UnauthorizedException;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Traits\FractalTrait;
//use Phalcon\Api\Traits\QueryTrait;
//use Phalcon\Api\Traits\ResponseTrait;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;
//use Phalcon\Mvc\Controller;
//use Phalcon\Mvc\Micro;
//use Phalcon\Mvc\Model\MetaData\Libmemcached as ModelsMetadataCache;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\ResultsetInterface;
use function explode;
//use function implode;
use function in_array;
use function strtolower;
//use function substr;

/**
 * Class BaseItemApiController
 * Also used in POST / PUT / DELETE endpoints
 *
 * @see https://docs.phalcon.io/4.0/en/controllers
 * @property Cache               $cache
 * @property Config              $config
 * @property Response            $response
 * #property Micro               $application
 * #property ModelsMetadataCache $modelsMetadata
 */
abstract class BaseItemApiController extends BaseController
{
    use FractalTrait;
//    use QueryTrait;
//    use ResponseTrait;

    /** @var string */
    protected $model = '';

    /** @var array */
    protected $includes = [];

    /** @var string */
    protected $method = 'item'; //Valid methods: item or collection

    /** @var string */
    protected $resource = '';

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /**
     * Check the ids
     * Call checkSecurity (if defined) with the ids
     * Call coreAction if defined
     * Otherwise get the model & format
     */
    public function callAction(): ResponseInterface
    {
        $parameters = $this->getRequestIds(func_get_args());
        if (method_exists($this, 'checkSecurity')) {
            $parameters = $this->checkSecurity($parameters);
        }
        // If the request is a POST / PUT / DELETE operation
        if (method_exists($this, 'coreAction')) {
            return $this->coreAction($parameters);
        }

        $data = ($this->method === 'item') ? $this->findRecord($parameters) : $this->findRecords($parameters);

        if ($data instanceof Response) {
            return $data;
        }
        return $this->sendApiData($data);
    }

//    protected function checkSecurity(array $parameters): array
//    {
//        if (!$this->auth->getUser()) {
//            throw new UnauthorizedException('Only available to registered users');
//        }
//        return $parameters;
//    }

//    abstract protected function coreAction(array $parameters): ResponseInterface;

    protected function getRequestIds($args): array
    {
        $parameters = [];
        $key = 'id';
        foreach ($args as $arg) {
            $parameters[$key] = $this->checkId($arg);
            $key = 'sub.'.$key;
        }
        return $parameters;
    }


    protected function findRecord(array $parameters)
    {
        $results = $this->getRecords($parameters);
        if (count($parameters) > 0 && 0 === $results->count()) {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::NOT_FOUND);
        }
        if ($this->method !== 'item'|| $results->count() !== 1) {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::INTERNAL_SERVER_ERROR);
        }
        return $results->getFirst();
    }

    /**
     * Runs a query using the builder
     *
     * @param array        $parameters
     * @param string       $orderBy
     * @return ResultsetInterface
     */
    protected function getRecords(array $parameters = [], string $orderBy = ''): ResultsetInterface
    {
        $builder = new Builder();
        $builder->setDI($this->getDI());
//        $builder = $this->modelsManager->createBuilder();
        $builder->addFrom($this->model, 't1');

        foreach ($parameters as $field => $value) {
            $builder->andWhere(
                sprintf('%s = :%s:', $field, $field),
                [ $field => $value ]
            );
        }

        if (true !== empty($orderBy)) {
            $builder->orderBy($orderBy);
        }

        return $this->getResultsCache($builder, $parameters);
    }

    /**
     * Runs the builder query if there is no cached data
     *
     * @param Builder      $builder
     * @param array        $parameters
     * @return ResultsetInterface
     */
    protected function getResultsCache(Builder $builder, array $parameters = []): ResultsetInterface
    {
        $cacheKey = CacheKeys::getQueryCacheKey($builder->getPhql(), implode(',', $parameters));
        if (true !== $this->config->path('app.devMode') && true === $this->cache->has($cacheKey)) {
            /** @var ResultsetInterface $data */
            $data = $this->cache->get($cacheKey);
        } else {
            $data = $builder->getQuery()->execute();
            $this->cache->set($cacheKey, $data);
        }

        return $data;
    }

    protected function sendApiData($data): ResponseInterface
    {
        $fields     = $this->checkFields();
        $related    = $this->checkIncludes();
        return $this->response
            ->setStatusCode(
                $this->response::OK,
                $this->response->getHttpCodeDescription($this->response::OK)
            )
            ->sendApiContent($this->request->getContentType(), $this->format(
                $this->method,
                $data,
                $this->transformer,
                $this->resource,
                $related,
                $fields
            ));
    }

    protected function checkFields(): array
    {
        $data      = [];
        $fieldSent = $this->request->getQuery('fields', [Filter::FILTER_STRING, Filter::FILTER_TRIM], []);
        foreach ($fieldSent as $resource => $fields) {
            $data[$resource] = explode(',', $fields);
        }

        return $data;
    }

    /**
     * Processes the includes requested; Unknown includes are ignored
     *
     * @return array
     */
    protected function checkIncludes(): array
    {
        $related  = [];
        $includes = $this->request->getQuery('includes', [Filter::FILTER_STRING, Filter::FILTER_TRIM], '');
        if (true !== empty($includes)) {
            $requestedIncludes = explode(',', $includes);
            foreach ($requestedIncludes as $include) {
                if (true === in_array($include, $this->includes, true)) {
                    $related[] = strtolower($include);
                }
            }
        }

        return $related;
    }
}
