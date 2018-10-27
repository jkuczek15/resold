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
 * @package   mirasvit/module-search
 * @version   1.0.100
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Model\Index;

use Magento\Search\Model\QueryFactory;
use Magento\CatalogSearch\Model\Advanced\Request\BuilderFactory as RequestBuilderFactory;
use Magento\Search\Model\SearchEngine;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\App\ScopeResolverInterface;
use Mirasvit\Search\Api\Data\Index\InstanceInterface;
use Mirasvit\Search\Api\Data\IndexInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Searcher
{
    /**
     * @var AbstractIndex
     */
    protected $index;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var RequestBuilderFactory
     */
    protected $requestBuilderFactory;

    /**
     * @var SearchEngine
     */
    protected $searchEngine;

    /**
     * @var TemporaryStorageFactory
     */
    protected $temporaryStorageFactory;

    /**
     * @var ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var ScopeResolverInterface
     */
    protected $indexRepository;

    /**
     * Constructor
     *
     * @param QueryFactory $queryFactory
     * @param RequestBuilderFactory $requestBuilderFactory
     * @param SearchEngine $searchEngine
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param ScopeResolverInterface $scopeResolver
     */
    public function __construct(
        QueryFactory $queryFactory,
        RequestBuilderFactory $requestBuilderFactory,
        SearchEngine $searchEngine,
        TemporaryStorageFactory $temporaryStorageFactory,
        ScopeResolverInterface $scopeResolver,
        IndexRepositoryInterface $indexRepository
    ) {
        $this->queryFactory = $queryFactory;
        $this->requestBuilderFactory = $requestBuilderFactory;
        $this->searchEngine = $searchEngine;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->scopeResolver = $scopeResolver;
        $this->indexRepository = $indexRepository;
    }

    /**
     * Set search index
     *
     * @param AbstractIndex $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return \Magento\Framework\Search\Response\QueryResponse|\Magento\Framework\Search\ResponseInterface
     */
    public function getQueryResponse()
    {
        $requestBuilder = $this->requestBuilderFactory->create();

        $queryText = $this->queryFactory->get()->getQueryText();

        $requestBuilder->bind('search_term', $queryText);

        $requestBuilder->bindDimension('scope', $this->scopeResolver->getScope());

        $requestBuilder->setRequestName($this->index->getIdentifier());

        $requestBuilder->setFrom([
            'index_name' => $this->index->getIndexName(),
            'index_id'   => $this->index->getModel()->getId(),
        ]);

        $queryRequest = $requestBuilder->create();

        return $this->searchEngine->search($queryRequest);
    }

    /**
     * Join matches to collection
     *
     * @param AbstractDb $collection
     * @param string $field
     *
     * @return $this
     */
    public function joinMatches($collection, $field = 'e.entity_id')
    {
        $queryResponse = $this->getQueryResponse();

        $temporaryStorage = $this->temporaryStorageFactory->create();

        if ($field == 'ID') {
            //external connection (need improve detection)
            $ids = [0];
            foreach ($queryResponse->getIterator() as $item) {
                $ids[] = $item->getId();
            }

            $collection->getSelect()->where(new \Zend_Db_Expr("$field IN (" . implode(',', $ids) . ")"));
        } else {
            $table = $temporaryStorage->storeDocuments($queryResponse->getIterator());

            $collection->getSelect()->joinInner(
                ['search_result' => $table->getName()],
                $field . ' = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
                []
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMatchedIds()
    {
        $requestBuilder = $this->requestBuilderFactory->create();
        $queryText = $this->queryFactory->get()->getQueryText();

        $requestBuilder->bind('search_term', $queryText);

        $requestBuilder->bindDimension('scope', $this->scopeResolver->getScope());

        $requestBuilder->setRequestName($this->index->getIdentifier());

        $requestBuilder->setFrom([
            'index_name' => $this->index->getIndexName(),
            'index_id'   => $this->index->getModel()->getId(),
        ]);

        /** @var \Magento\Framework\Search\Request $queryRequest */
        $queryRequest = $requestBuilder->create();

        $queryResponse = $this->searchEngine->search($queryRequest);
        $ids = [];
        foreach ($queryResponse->getIterator() as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }
}
