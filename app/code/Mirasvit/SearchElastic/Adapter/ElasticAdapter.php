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

use Mirasvit\SearchElastic\Model\Config;
use Mirasvit\SearchElastic\Model\Engine;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Adapter\Mysql\ResponseFactory;
use Mirasvit\SearchElastic\Adapter\Aggregation\Builder as AggregationBuilder;
use Magento\Framework\Search\Adapter\Mysql\Adapter as MysqlAdapter;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ElasticAdapter implements AdapterInterface
{
    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MysqlAdapter
     */
    private $mysqlAdapter;

    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        Engine $engine,
        Config $config,
        MysqlAdapter $mysqlAdapter
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->engine = $engine;
        $this->config = $config;
        $this->mysqlAdapter = $mysqlAdapter;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     * @SuppressWarnings(PHPMD)
     */
    public function query(RequestInterface $request)
    {
        $client = $this->engine->getClient();
        $query = $this->mapper->buildQuery($request);

        if (!$this->engine->isAvailable()) {
            return $this->mysqlAdapter->query($request);
        }

        if ($request->getName() == 'quick_search_container'
            || $request->getName() == 'catalog_view_container'
            || $request->getName() == 'catalogsearch_fulltext'
        ) {
            $query = $this->filterByStockStatus($query);
        }

        if (isset($_GET['debug'])) {
            echo "<pre>";
            print_r($query);
            echo "</pre>";
        }

        $attempt = 0;
        $response = false;
        $exception = false;

        while ($attempt < 5 && $response === false) {
            $attempt++;

            try {
                $response = $client->search($query);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }

        if (!$response && $exception) {
            throw $exception;
        }

        if (isset($_GET['debug'])) {
            echo "<pre>";
            print_r($response);
            echo "</pre>";
        }

        $hits = isset($response['hits']['hits']) ? $response['hits']['hits'] : [];

        $documents = [];
        foreach ($hits as $doc) {
            $d = [
                'id'        => $doc['_id'],
                'entity_id' => $doc['_id'],
                'score'     => $doc['_score'],
                'data'      => isset($doc['_source']) ? $doc['_source'] : [],
            ];

            $documents[] = $d;
        }

        return $this->responseFactory->create([
            'documents'    => $documents,
            'aggregations' => $this->aggregationBuilder->extract($request, $response),
        ]);
    }

    /**
     * @param array $query
     * @return array
     */
    private function filterByStockStatus($query)
    {
        if ($this->config->isShowOutOfStock() == false) {
            $query['body']['query']['bool']['must'][] = [
                'term' => [
                    'is_in_stock_raw' => 1,
                ],
            ];
        }

        return $query;
    }

    /**
     * @param object $object
     * @param int $indent
     * @return void
     */
    public function superPrint($object, $indent = 0)
    {
        if (is_object($object)) {
            $methods = get_class_methods($object);
            foreach ($methods as $method) {
                if (substr($method, 0, 3) == 'get') {
                    echo str_repeat(' ', $indent * 5) . $method . ' => ' . PHP_EOL;
                    $this->superPrint(call_user_func([$object, $method]), $indent + 1);
                }
            }
        } elseif (is_scalar($object)) {
            echo str_repeat(' ', $indent * 5) . $object . PHP_EOL;
        } elseif (is_array($object)) {
            foreach ($object as $key => $item) {
                echo str_repeat(' ', $indent * 5) . $key . ' => ' . PHP_EOL;
                $this->superPrint($item, $indent + 1);
            }
        }
    }
}
