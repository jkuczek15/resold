<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-elastic
 * @version   1.2.26
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Adapter;

use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Adapter\Mysql\IndexBuilderInterface;
use Magento\Framework\Search\Request\Query\BoolExpression as BoolQuery;
use Magento\Framework\Search\Request\Query\Filter as FilterQuery;
use Magento\Framework\Search\Request\Query\Match as MatchQuery;
use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;
use Magento\Framework\Search\RequestInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;
use Mirasvit\SearchElastic\Model\Config;
use Mirasvit\SearchElastic\Model\Indexer\ScopeProxy;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mapper
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var IndexBuilderInterface[]
     */
    private $indexProviders;

    /**
     * @var Query\MatchQuery
     */
    private $matchQuery;

    /**
     * @var Query\FilterQuery
     */
    private $filterQuery;

    /**
     * @var Query\AggregationQuery
     */
    private $aggregationQuery;

    /**
     * @var ScopeProxy
     */
    private $resolver;

    public function __construct(
        IndexRepositoryInterface $indexRepository,
        ConditionManager $conditionManager,
        Query\MatchQuery $matchQuery,
        Query\FilterQuery $filterQuery,
        Query\AggregationQuery $aggregationQuery,
        Config $config,
        ScopeProxy $resolver,
        array $indexProviders
    ) {
        $this->indexRepository = $indexRepository;
        $this->conditionManager = $conditionManager;
        $this->indexProviders = $indexProviders;
        $this->matchQuery = $matchQuery;
        $this->resolver = $resolver;
        $this->config = $config;
        $this->filterQuery = $filterQuery;
        $this->aggregationQuery = $aggregationQuery;
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function buildQuery(RequestInterface $request)
    {
        if (is_array($request->getFrom())) {
            $indexName = $this->resolver->resolve(
                $request->getFrom()['index_name'],
                $request->getDimensions()
            );
        } else {
            $searchIndex = $this->indexRepository->get($request->getIndex());
            $indexName = $this->resolver->resolve(
                $searchIndex->getIdentifier(),
                $request->getDimensions()
            );
        }

        $query = [
            'index' => $this->config->getIndexName($indexName),
            'type'  => Config::DOCUMENT_TYPE,
            'body'  => [
                'from'          => is_scalar($request->getFrom()) ? $request->getFrom() : 0,
                'size'          => 1000000 - 1,//$request->getSize(), magento apply sorting AFTER selecting products, so we should return products without limits
                'stored_fields' => ['_id', '_score'],
                'query'         => $this->processQuery($request->getQuery(), [], BoolQuery::QUERY_CONDITION_MUST),
                //'_source'       => [],
            ],
        ];

        $aggregations = $this->aggregationQuery->build($request);

        if ($aggregations) {
            $query['body']['aggregations'] = $aggregations;
        }

        return $query;
    }

    /**
     * @param RequestQueryInterface $requestQuery
     * @param array $query
     * @param string $conditionType
     * @return array
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function processQuery(RequestQueryInterface $requestQuery, array $query, $conditionType)
    {
        switch ($requestQuery->getType()) {
            case RequestQueryInterface::TYPE_BOOL:
                /** @var BoolQuery $requestQuery */
                $query = $this->processBoolQuery($requestQuery, $query);
                break;

            case RequestQueryInterface::TYPE_MATCH:
                /** @var MatchQuery $requestQuery */
                $query = $this->matchQuery->build($query, $requestQuery);
                break;

            case RequestQueryInterface::TYPE_FILTER:
                /** @var FilterQuery $requestQuery */
                $query = $this->processFilterQuery($requestQuery, $query);
                break;

            default:
                throw new \Exception("Not implemented");
        }

        return $query;
    }

    /**
     * @param BoolQuery $query
     * @param array $selectQuery
     * @return array
     */
    protected function processBoolQuery(BoolQuery $query, array $selectQuery)
    {
        $selectQuery = $this->processBoolQueryCondition(
            $query->getMust(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_MUST
        );

        $selectQuery = $this->processBoolQueryCondition(
            $query->getShould(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_SHOULD
        );

        $selectQuery = $this->processBoolQueryCondition(
            $query->getMustNot(),
            $selectQuery,
            BoolQuery::QUERY_CONDITION_NOT
        );

        return $selectQuery;
    }

    /**
     * @param array $subQueryList
     * @param array $query
     * @param string $conditionType
     * @return array
     */
    protected function processBoolQueryCondition(array $subQueryList, array $query, $conditionType)
    {
        foreach ($subQueryList as $subQuery) {
            $query = $this->processQuery($subQuery, $query, $conditionType);
        }

        return $query;
    }

    /**
     * @param FilterQuery $query
     * @param array $selectQuery
     * @return array
     * @throws \Exception
     */
    protected function processFilterQuery(FilterQuery $query, array $selectQuery)
    {
        switch ($query->getReferenceType()) {
            case FilterQuery::REFERENCE_FILTER:
                $filterQuery = $this->filterQuery->build($query->getReference());
                foreach ($filterQuery['bool'] as $condition => $filter) {
                    $selectQuery['bool'][$condition] = array_merge(
                        isset($selectQuery['bool'][$condition]) ? $selectQuery['bool'][$condition] : [],
                        $filter
                    );
                }
                break;
            default:
                throw new \Exception("Filter query not implemented");
        }

        return $selectQuery;
    }
}
