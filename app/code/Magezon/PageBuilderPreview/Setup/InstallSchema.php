<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilderPreview
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilderPreview\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mgz_pagebuilder_preview_profile'
         */
        $table = $installer->getConnection()
        ->newTable($installer->getTable('mgz_pagebuilder_preview_profile'))
        ->addColumn(
            'profile_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Profile ID'
        )->addColumn(
            'builder_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            ' Builder ID'
        )->addColumn(
            'content',
            Table::TYPE_TEXT,
            '64M',
            ['nullable' => false],
            'Content'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Profile Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Profile Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('mgz_pagebuilder_preview_profile'),
                ['content'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['content'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'MGZ Page Builder Preview Profile Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
