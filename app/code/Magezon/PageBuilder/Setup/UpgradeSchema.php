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
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create table 'mgz_pagebuilder_template'
         */
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()
        ->newTable($installer->getTable('mgz_pagebuilder_template'))
        ->addColumn(
            'template_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Template ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'profile',
            Table::TYPE_TEXT,
            '64M',
            ['nullable' => false],
            'Elements'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Is Template Active'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Template Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Template Modification Time'
        )->addIndex(
            $installer->getIdxName(
                $installer->getTable('mgz_pagebuilder_template'),
                ['name', 'profile'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name', 'profile'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'MGZ Page Builder Template Table'
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable('cms_page'),
            'content',
            'content',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => '64M',
                'comment' => 'Content'
            ]
        );

        $installer->getConnection()->changeColumn(
            $installer->getTable('cms_block'),
            'content',
            'content',
            [
                'type'    => Table::TYPE_TEXT,
                'length'  => '64M',
                'comment' => 'Content'
            ]
        );

        $installer->endSetup();
    }
}