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

use Mirasvit\Search\Api\Data\IndexInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogSearch\Model\Indexer\IndexerHandlerFactory;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;

class Indexer
{
    /**
     * @var AbstractIndex
     */
    protected $index;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var IndexerHandlerFactory
     */
    protected $indexHandlerFactory;

    /**
     * @var IndexScopeResolver
     */
    protected $indexScopeResolver;

    public function __construct(
        StoreManagerInterface $storeManager,
        IndexerHandlerFactory $indexHandlerFactory,
        IndexScopeResolver $indexScopeResolver
    ) {
        $this->storeManager = $storeManager;
        $this->indexHandlerFactory = $indexHandlerFactory;
        $this->indexScopeResolver = $indexScopeResolver;
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
     * Index name by store id
     *
     * @param int $storeId
     * @return string
     */
    public function getIndexName($storeId)
    {
        $dimension = new Dimension('scope', $storeId);

        return $this->indexScopeResolver->resolve($this->index->getIndexName(), [$dimension]);
    }

    /**
     * Reindex all stores
     *
     * @param int $storeId
     * @return bool
     */
    public function reindexAll($storeId = null)
    {
        $indexName = $this->index->getIndexName();

        $configData = [
            'fieldsets'  => [],
            'indexer_id' => $indexName,
            'index_id'   => $this->index->getModel()->getId(),
        ];

        /** @var \Magento\CatalogSearch\Model\Indexer\IndexerHandler $indexHandler */
        $indexHandler = $this->indexHandlerFactory->create(['data' => $configData]);

        $storeIds = array_keys($this->storeManager->getStores());
        foreach ($storeIds as $id) {
            if ($storeId && $storeId != $id) {
                continue;
            }

            $dimension = new Dimension('scope', $id);
            $indexHandler->cleanIndex([$dimension]);
            $indexHandler->saveIndex(
                [$dimension],
                $this->rebuildStoreIndex($id)
            );
        }

        return true;
    }

    /**
     * Rebuild store index
     *
     * @param int $storeId
     * @param null|array $ids
     * @return void
     */
    public function rebuildStoreIndex($storeId, $ids = null)
    {
        if (!is_array($ids) && $ids != null) {
            $ids = [$ids];
        }

        $pk = $this->index->getPrimaryKey();

        $attributes = array_keys($this->index->getAttributeWeights());

        $lastEntityId = 0;
        while (true) {
            $collection = $this->index->getSearchableEntities($storeId, $ids, $lastEntityId);

            if ($collection->count() == 0) {
                break;
            }

            /** @var DataObject $entity */
            foreach ($collection as $entity) {
                $document = [];

                foreach ($attributes as $attribute) {
                    $attributeId = $this->index->getAttributeId($attribute);
                    $attributeValue = $entity->getData($attribute);

                    $document[$attributeId] = $attributeValue;
                }

                yield $entity->getData($pk) => $document;

                $lastEntityId = $entity->getData($pk);
            }
        }
    }
}
