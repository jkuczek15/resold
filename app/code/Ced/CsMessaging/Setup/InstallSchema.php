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
  * @category  Ced
  * @package   Ced_CsMessaging
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsMessaging\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'advanced_matrixrate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_csmessaging')
        )->addColumn(
            'chat_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Chat Id'
        )->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Subject'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Message'  
        )->addColumn(
            'sender_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['nullable' => false, 'default' => '0'],
            'Sender Id'
        )->addColumn(
            'receiver_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Receiver Name'
        )->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Sender Email'
        )->addColumn(
            'receiver_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Receiver Email'
        )->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '12,4',
            ['nullable' => false, 'default' => ''],
            'Date'
        )->addColumn(
            'time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '12,4',
            ['nullable' => false, 'default' => ''],
            'Time'
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '12,4',
            ['nullable' => false, 'default' => ''],
            'Vendor Id'
        )->addColumn(
            'vcount',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '12,4',
            ['nullable' => false, 'default' => ''],
            'Vcount'
        )->addColumn(
            'role',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Role'
        )->addColumn(
            'postread',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => ''],
            'Post Read'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

    }
}
