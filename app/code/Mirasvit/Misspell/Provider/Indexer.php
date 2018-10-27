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
 * @package   mirasvit/module-misspell
 * @version   1.0.27
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Misspell\Provider;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Mirasvit\Misspell\Helper\Text as TextHelper;

class Indexer
{
    /**
     * @var array
     */
    private $allowedTables = [
        'catalogsearch_fulltext',
        'mst_searchindex_',
        'catalog_product_entity_text',
        'catalog_product_entity_varchar',
        'catalog_category_entity_text',
        'catalog_category_entity_varchar',
    ];

    /**
     * @var array
     */
    private $disallowedTables = [
        'mst_searchindex_mage_catalogsearch_query',
    ];

    /**
     * @var \Magento\Framework\App\Resource
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var \Mirasvit\Misspell\Helper\Text
     */
    private $text;

    public function __construct(
        ResourceConnection $resource,
        TextHelper $textHelper
    ) {
        $this->resource = $resource;
        $this->connection = $this->resource->getConnection();
        $this->text = $textHelper;
    }

    /**
     * @return void
     */
    public function reindex()
    {
        $results = [];
        foreach ($this->getTables() as $table => $columns) {
            if (!count($columns)) {
                continue;
            }

            foreach ($columns as $idx => $col) {
                $columns[$idx] = '`' . $col . '`';
            }

            $select = $this->connection->select();
            $fromColumns = new \Zend_Db_Expr("CONCAT_WS(' '," . implode(',', $columns) . ") as data_index");
            $select->from($table, $fromColumns);

            $result = $this->connection->query($select);
            while ($row = $result->fetch()) {
                $data = $row['data_index'];

                $this->split($data, $results);
            }
        }

        $indexTable = $this->resource->getTableName('mst_misspell_index');
        $this->connection->delete($indexTable);

        $rows = [];
        foreach ($results as $word => $freq) {
            $rows[] = [
                'keyword'   => $word,
                'trigram'   => $this->text->getTrigram($word),
                'frequency' => $freq / count($results),
            ];

            if (count($rows) > 1000) {
                $this->connection->insertArray($indexTable, ['keyword', 'trigram', 'frequency'], $rows);
                $rows = [];
            }
        }

        if (count($rows) > 0) {
            $this->connection->insertArray($indexTable, ['keyword', 'trigram', 'frequency'], $rows);
        }

        $this->connection->delete($this->resource->getTableName('mst_misspell_suggest'));
    }

    /**
     * Split string to words
     *
     * @param string $string
     * @param array &$results
     * @param int $increment
     * @return void
     */
    protected function split($string, &$results, $increment = 1)
    {
        $string = $this->text->cleanString($string);
        $words = $this->text->splitWords($string);

        foreach ($words as $word) {
            if ($this->text->strlen($word) >= $this->text->getGram()
                && !is_numeric($word)
                && $this->text->strlen($word) <= 10
            ) {
                $word = $this->text->strtolower($word);
                if (!isset($results[$word])) {
                    $results[$word] = $increment;
                } else {
                    $results[$word] += $increment;
                }
            }
        }
    }

    /**
     * List of tables that follow allowedTables, disallowedTables conditions
     *
     * @return array
     */
    protected function getTables()
    {
        $result = [];
        $tables = $this->connection->getTables();

        foreach ($tables as $table) {
            $isAllowed = false;

            foreach ($this->allowedTables as $allowedTable) {
                if (mb_strpos($table, $allowedTable) !== false) {
                    $isAllowed = true;
                }
            }

            foreach ($this->disallowedTables as $disallowedTable) {
                if (mb_strpos($table, $disallowedTable) !== false) {
                    $isAllowed = false;
                }
            }

            if (!$isAllowed) {
                continue;
            }

            $result[$table] = $this->getTextColumns($table);
        }

        return $result;
    }

    /**
     * Text columns
     *
     * @param string $table Database table name
     * @return array list of columns with text type
     */
    protected function getTextColumns($table)
    {
        $result = [];
        $allowedTypes = ['text', 'varchar', 'mediumtext', 'longtext'];
        $columns = $this->connection->describeTable($table);

        foreach ($columns as $column => $info) {
            if (in_array($info['DATA_TYPE'], $allowedTypes)) {
                $result[] = $column;
            }
        }

        return $result;
    }
}
