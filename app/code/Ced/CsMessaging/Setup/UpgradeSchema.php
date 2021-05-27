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
 * @package     Ced_CsMessaging
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMessaging\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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
        $tableName_vendor=$installer->getTable('ced_csmessaging');

        if ($installer->getConnection()->isTableExists($tableName_vendor) == true) {
            $connection = $setup->getConnection();
            $connection
            ->addColumn(
                $tableName_vendor,
                'send_mail',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                0,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Send Mail'
            );
            $connection->addColumn(
                $tableName_vendor,
                'is_mail_sent',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                0,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Mail sent'
            );
            $connection->addColumn(
                $tableName_vendor,
                'send_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0', 'length'  => 255],
                'send to'
            );
        }
        $installer->endSetup();
    }
}
