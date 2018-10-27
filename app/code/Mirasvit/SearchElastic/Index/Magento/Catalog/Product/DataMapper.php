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



namespace Mirasvit\SearchElastic\Index\Magento\Catalog\Product;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Search\Api\Data\Index\DataMapperInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\ProductMetadataInterface;

class DataMapper implements DataMapperInterface
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var StockRegistryInterface
     */
    private $stockState;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    static $attributeCache = [];

    public function __construct(
        IndexRepositoryInterface $indexRepository,
        EavConfig $eavConfig,
        StockRegistryInterface $stockState,
        ResourceConnection $resource,
        ProductMetadataInterface $productMetadata
    ) {
        $this->indexRepository = $indexRepository;
        $this->eavConfig = $eavConfig;
        $this->stockState = $stockState;
        $this->resource = $resource;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param array $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param string $indexIdentifier
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        $dimension = current($dimensions);

        $rawDocs = [];
        foreach (['catalog_product_entity_varchar', 'catalog_product_entity_text', 'catalog_product_entity_decimal', 'catalog_product_index_eav'] as $table) {
            $dt = $this->eavMap($table, array_keys($documents), $dimension->getValue());

            foreach ($dt as $row) {
                $entityId = isset($row['row_id']) ? $row['row_id'] : $row['entity_id'];
                $attributeId = $row['attribute_id'];

                if (!isset(self::$attributeCache[$attributeId])) {
                    self::$attributeCache[$attributeId] = $this->eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeId);
                }

                $attribute = self::$attributeCache[$attributeId];
                $rawDocs[$entityId][$attribute->getAttributeCode()][] = $row['value'];
            }
        }


        foreach ($documents as $id => $doc) {
            $rawData = @$rawDocs[$id];
            $rawData['is_in_stock'] = $this->stockState->getStockStatus($id)->getStockStatus() ? 1 : 0;

            foreach ($doc as $key => $value) {
                if (is_array($value) && !in_array($key, ['autocomplete_raw', 'autocomplete'])) {
                    $doc[$key] = implode(' ', $value);

                    foreach ($value as $v) {
                        $doc[$key . '_raw'][] = intval($v);
                    }
                }
            }

            foreach ($rawData as $attribute => $value) {
                if (is_scalar($value) || is_array($value)) {
                    if ($attribute != 'media_gallery'
                        && $attribute != 'options_container'
                        && $attribute != 'quantity_and_stock_status'
                        && $attribute != 'country_of_manufacture'
                        && $attribute != 'tier_price'
                    ) {
                        $doc[$attribute . '_raw'] = $value;
                    }
                }
            }

            $documents[$id] = $doc;
        }

        $productIds = array_keys($documents);

        $categoryIds = $this->getCategoryProductIndexData($productIds, $dimension->getValue());

        foreach ($documents as $id => $doc) {
            $doc['category_ids_raw'] = isset($categoryIds[$id]) ? $categoryIds[$id] : [];
            $documents[$id] = $doc;
        }

        return $documents;
    }

    /**
     * @param array $productIds
     * @param array $storeId
     * @return array
     */
    private function getCategoryProductIndexData($productIds, $storeId)
    {
        $productIds[] = 0;

        $connection = $this->resource->getConnection();

        $tableName = $this->resource->getTableName('catalog_category_product_index') . '_store' . $storeId;
        if (!$this->resource->getConnection()->isTableExists($tableName)) {
            $tableName = $this->resource->getTableName('catalog_category_product_index');
        }

        $select = $connection->select()->from(
            [$tableName],
            ['category_id', 'product_id']
        );

        $select->where('product_id IN (?)', $productIds);

        $result = [];
        foreach ($connection->fetchAll($select) as $row) {
            $result[$row['product_id']][] = $row['category_id'];
        }

        $select = $connection->select()->from(
            [$this->resource->getTableName('catalog_category_product')],
            ['category_id', 'product_id']
        );

        $select->where('product_id IN (?)', $productIds);

        foreach ($connection->fetchAll($select) as $row) {
            $result[$row['product_id']][] = $row['category_id'];
            $result[$row['product_id']] = array_values(array_unique($result[$row['product_id']]));
        }

        return $result;
    }

    private function eavMap($table, $ids, $storeId)
    {
        $select = $this->resource->getConnection()->select();

        $select->from(
            ['eav' => $this->resource->getTableName($table)],
            ['*']
        )->where('eav.store_id in (0, ?)', $storeId);

        if (($this->productMetadata->getEdition() == 'Enterprise'
                || $this->productMetadata->getEdition() == 'B2B'
            )
            && $table != 'catalog_product_index_eav') {
            $select->where('eav.row_id in (?)', $ids);
        } else {
            $select->where('eav.entity_id in (?)', $ids);
        }

        return $this->resource->getConnection()->fetchAll($select);
    }
}
