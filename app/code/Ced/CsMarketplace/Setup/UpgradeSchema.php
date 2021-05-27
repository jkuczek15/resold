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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager=$objectManager;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('ced_csmarketplace_vendor_form_attribute');
        $tableName_vendor = $installer->getTable('ced_csmarketplace_vendor');
        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $connection = $setup->getConnection();
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $connection->addColumn($tableName,
                    'use_in_registration',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'Use in Registration Form'
                );
                $connection->addColumn($tableName,
                    'position_in_registration',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'Position in Registration Form'
                );
                $connection->addColumn($tableName,
                    'use_in_left_profile',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'Use in Left Profile'
                );
                $connection->addColumn($tableName,
                    'position_in_left_profile',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0', 'length'  => 50),
                    'Position in Left Profile'
                );
                $connection->addColumn($tableName,
                    'fontawesome_class_for_left_profile',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0', 'length'  => 200),
                    'Fontawesome class for Left Profile'
                );
            }

            if ($installer->getConnection()->isTableExists($tableName_vendor) == true) {
                $connection->addColumn( $tableName_vendor,
                    'address',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'Address'
                );
                $connection->addColumn($tableName_vendor,
                    'city',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'City'
                );
                $connection->addColumn($tableName_vendor,
                    'zip_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    array('unsigned' => true, 'nullable' => false, 'default' => '0'),
                    'Zip/Postal Code'
                );
            }
        }   

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tableName = $installer->getTable('ced_csmarketplace_vendor_products');
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\ResourceModel\Vproducts\Collection')
                                ->addFieldToFilter('check_status', 3)
                                ->walk('delete');
                $connection = $setup->getConnection();
                $connection->rawQuery("ALTER TABLE `".$tableName."` ADD UNIQUE(`product_id`)");   
            }
        }
        
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
        	$tableName = $installer->getTable('ced_csmarketplace_vendor_products');
        	if ($installer->getConnection()->isTableExists($tableName) == true) {
        		$connection = $setup->getConnection();
        		$connection->rawQuery("ALTER TABLE `".$tableName."`  ADD CONSTRAINT `ced_csmarketplace_vendor_product_vendor_id_to_fk` FOREIGN KEY (`vendor_id` ) REFERENCES `".$installer->getTable('ced_csmarketplace_vendor')."` (`entity_id` ) ON DELETE CASCADE");
        	}
        }
        
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
        	$table = $installer->getTable('ced_csmarketplace_vendor_sales_order');
        	if ($installer->getConnection()->isTableExists($table) == true) {
        		$columns = [
            		'billing_name' => [
                		'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                		'nullable' => false,
                		'comment' => 'Billing Name'
            		],
    				'created_at' => [
        				'type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
        				'nullable' =>false,
        				'default' =>\Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
        				'comment' => 'Created At'
        			]
				];
				$connection = $installer->getConnection();
				foreach ($columns as $name => $value) {
				    $connection->addColumn($table, $name, $value);
                }
        	}
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $vendorProduct = $installer->getTable('ced_csmarketplace_vendor_products');
            $connection = $setup->getConnection();
            if ($installer->getConnection()->isTableExists($vendorProduct) == true) {
                $connection->rawQuery("ALTER TABLE `".$vendorProduct."`  ADD CONSTRAINT `ced_csmarketplace_vendor_products_product_id_to_fk` FOREIGN KEY (`product_id` ) REFERENCES `".$installer->getTable('catalog_product_entity')."` (`entity_id` ) ON DELETE CASCADE");
            }
            $vendorProductStatus = $installer->getTable('ced_csmarketplace_vendor_products_status');
            if ($installer->getConnection()->isTableExists($vendorProductStatus) == true) {
                $connection->rawQuery("ALTER TABLE `".$vendorProductStatus."`  ADD CONSTRAINT `ced_csmarketplace_vendor_products_status_product_id_to_fk` FOREIGN KEY (`product_id` ) REFERENCES `".$installer->getTable('catalog_product_entity')."` (`entity_id` ) ON DELETE CASCADE");
            }

            /**
             * Create table 'ced_csmarketplace_notification'
             */
            $table = $installer->getConnection()->newTable($installer->getTable('ced_csmarketplace_notification'))
            ->addColumn('id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn('action',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'action'
            )->addColumn('title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'title'
            )->addColumn('itag',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'itag'
            )->addColumn('vendor_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'vendor_id'
            )->addColumn('created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn('updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->addColumn('reference_id',
               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'reference_id'
            )->addColumn('status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'status'
            )->addIndex(
                $installer->getIdxName('ced_csmarketplace_notifications_vendor_id', ['vendor_id']),
                ['id']
            )->setComment('Vendor Notifications Table');
            $installer->getConnection()->createTable($table);
        }


        $installer->endSetup();
    }
}
