<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Model\ResourceModel\Setup;
class AbstractModel extends \Magento\Eav\Model\Entity
{


    /**
     * Create entity tables
     * @param $baseTableName
     * @param array $options
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createEntityTables($baseTableName, array $options = array())
    {
        return $this->createEntityTablesAbove16($baseTableName, $options);
    }
    
    
    public function createEntityTablesAbove16($baseTableName, array $options = array())
    {
        $isNoCreateMainTable = $this->_getValue($options, 'no-main', false);
        $isNoDefaultTypes    = $this->_getValue($options, 'no-default-types', false);
        $customTypes         = $this->_getValue($options, 'types', array());
        $tables              = array();

        if (!$isNoCreateMainTable) {
            /**
             * Create table main eav table
             */
            $connection = $this->getConnection();
            $mainTable = $connection
                ->newTable($this->getTable($baseTableName))
                ->addColumn(
                    'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                    ), 'Entity Id'
                )
                ->addColumn(
                    'entity_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Type Id'
                )
                ->addColumn(
                    'attribute_set_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Attribute Set Id'
                )
                ->addColumn(
                    'increment_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 50, array(
                    'nullable'  => false,
                    'default'   => '',
                    ), 'Increment Id'
                )
                ->addColumn(
                    'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Store Id'
                )
                ->addColumn(
                    'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                    ), 'Created At'
                )
                ->addColumn(
                    'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                    ), 'Updated At'
                )
                ->addColumn(
                    'is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '1',
                    ), 'Defines Is Entity Active'
                )
                ->addIndex(
                    $this->getIdxName($baseTableName, array('entity_type_id')),
                    array('entity_type_id')
                )
                ->addIndex(
                    $this->getIdxName($baseTableName, array('store_id')),
                    array('store_id')
                )
                ->addForeignKey(
                    $this->getFkName($baseTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($baseTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id', $this->getTable('core/store'), 'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Eav Entity Main Table');

            $tables[$this->getTable($baseTableName)] = $mainTable;
        }

        $types = array();
        if (!$isNoDefaultTypes) {
            $types = array(
                'datetime'  => array(\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null),
                'decimal'   => array(\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4'),
                'int'       => array(\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null),
                'text'      => array(\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k'),
                'varchar'   => array(\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255'),
                'char'   => array(\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255')
            );
        }

        if (!empty($customTypes)) {
            foreach ($customTypes as $type => $fieldType) {
                if (count($fieldType) != 2) {
                    throw new \Magento\Framework\Exception\LocalizedException('Magento_Eav'.__('Wrong type definition for %1', $type));
                }
                $types[$type] = $fieldType;
            }
        }

        /**
         * Create table array($baseTableName, $type)
         */
        foreach ($types as $type => $fieldType) {
            $eavTableName = array($baseTableName, $type);

            $eavTable = $connection->newTable($this->getTable($eavTableName));
            $eavTable
                ->addColumn(
                    'value_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                    ), 'Value Id'
                )
                ->addColumn(
                    'entity_type_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Type Id'
                )
                ->addColumn(
                    'attribute_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Attribute Id'
                )
                ->addColumn(
                    'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Store Id'
                )
                ->addColumn(
                    'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Id'
                )
                ->addColumn(
                    'value', $fieldType[0], $fieldType[1], array(
                    'nullable'  => false,
                    ), 'Attribute Value'
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('entity_type_id')),
                    array('entity_type_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('attribute_id')),
                    array('attribute_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('store_id')),
                    array('store_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('entity_id')),
                    array('entity_id')
                );
            if ($type !== 'text') {
                $eavTable->addIndex(
                    $this->getIdxName($eavTableName, array('attribute_id', 'value')),
                    array('attribute_id', 'value')
                );
                $eavTable->addIndex(
                    $this->getIdxName($eavTableName, array('entity_type_id', 'value')),
                    array('entity_type_id', 'value')
                );
            }

            $eavTable
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'entity_id', $baseTableName, 'entity_id'),
                    'entity_id', $this->getTable($baseTableName), 'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id', $this->getTable('core/store'), 'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Eav Entity Value Table');

            $tables[$this->getTable($eavTableName)] = $eavTable;
        }

        try {
            foreach ($tables as $tableName => $table) {
                $connection->createTable($table);
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException('Magento_Eav'. __('Can\'t create table: %1', $tableName));
        }

        return $this;
    }
}
