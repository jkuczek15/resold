<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Plumrocket\GDPR\Model\ResourceModel\Revision as RevisionResource;
use Plumrocket\GDPR\Model\ResourceModel\Revision\History as RevisionHistoryResource;

/**
 * Module install schema.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(// @codingStandardsIgnoreLine
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'plumrocket_gdpr_removal_requests'
         */
        $tableDelete = $installer->getConnection()
            ->newTable($installer->getTable('plumrocket_gdpr_removal_requests'))
            ->addColumn(
                'request_id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id of requests item'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Request Date'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Website'
            )
            ->addColumn(
                'customer_ip',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer IP'
            )
            ->addColumn(
                'cancelled_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Cancellation Date'
            )
            ->addColumn(
                'cancelled_by',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Cancelled By'
            )
            ->addColumn(
                'scheduled_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => null],
                'Removal Date'
            )
            ->addColumn(
                'status',
                Table::TYPE_TEXT,
                32,
                ['nullable' => false, 'default' => 'pending'],
                'Status'
            )
            ->addIndex(
                $installer->getIdxName(
                    'plumrocket_gdpr_removal_requests',
                    ['customer_id'],
                    true
                ),
                ['customer_id'],
                ['type' => 'index']
            )
            ->setComment('Log of Account Removal Requests');

        $installer->getConnection()->createTable($tableDelete);

        /**
         * Create table 'plumrocket_gdpr_export_log'
         */
        $tableExport = $installer->getConnection()
            ->newTable($installer->getTable('plumrocket_gdpr_export_log'))
            ->addColumn(
                'log_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id of log item'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                'customer_ip',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer IP'
            )
            ->addIndex(
                $installer->getIdxName(
                    'plumrocket_gdpr_export_log',
                    ['customer_id'],
                    true
                ),
                ['customer_id'],
                ['type' => 'index']
            )->addForeignKey(
                $installer->getFkName('plumrocket_gdpr_export_log', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment('Log of Account Data Downloads');

        $installer->getConnection()->createTable($tableExport);

        /**
         * Create table 'plumrocket_gdpr_consents_log'
         */
        $tableDelete = $installer->getConnection()
            ->newTable($installer->getTable('plumrocket_gdpr_consents_log'))
            ->addColumn(
                'consent_id',
                Table::TYPE_INTEGER,
                11,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id of requests item'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Request Date'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Customer entity Id'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Website'
            )
            ->addColumn(
                'customer_ip',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer IP'
            )
            ->addColumn(
                'location',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Consent Location'
            )
            ->addColumn(
                'label',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Consent Label'
            )
            ->addColumn(
                'cms_page_id',
                Table::TYPE_SMALLINT,
                6,
                ['unsigned' => true],
                'Link to CMS Page'
            )
            ->addColumn(
                'version',
                Table::TYPE_TEXT,
                32,
                [],
                'Version'
            )
            ->addIndex(
                $installer->getIdxName(
                    'plumrocket_gdpr_consents_log',
                    ['customer_id'],
                    true
                ),
                ['customer_id'],
                ['type' => 'index']
            )->addForeignKey(
                $installer->getFkName('plumrocket_gdpr_consents_log', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Log of Customer Consents');

        $installer->getConnection()->createTable($tableDelete);

        /**
         * Create revision table
         */
        $revisionTable = $installer->getConnection()->newTable(
            $installer->getTable(RevisionResource::MAIN_TABLE_NAME)
        )->addColumn(
            'revision_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'cms_page_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'primary' => true
            ],
            'CMS Page ID'
        )->addColumn(
            'enable_revisions',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Enable Revisions'
        )->addColumn(
            'notify_via_popup',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Notify All Customers via Popup'
        )->addColumn(
            'document_version',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Document Version'
        )->addColumn(
            'popup_content',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Popup Content'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ],
            'Date Of Creation'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT_UPDATE
            ],
            'Date Of Modification'
        )->addIndex(
            $installer->getIdxName(
                RevisionResource::MAIN_TABLE_NAME,
                ['cms_page_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['cms_page_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                RevisionResource::MAIN_TABLE_NAME,
                'cms_page_id',
                'cms_page',
                'page_id'
            ),
            'cms_page_id',
            $installer->getTable('cms_page'),
            'page_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($revisionTable);

        /**
         * Create revision history table
         */
        $revisionHistoryTable = $installer->getConnection()->newTable(
            $installer->getTable(RevisionHistoryResource::MAIN_TABLE_NAME)
        )->addColumn(
            'history_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'revision_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'CMS Page ID'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ],
            'User ID'
        )->addColumn(
            'user_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'User Name'
        )->addColumn(
            'version',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Document Version'
        )->addColumn(
            'content',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Version Content'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ],
            'Date Of Creation'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT_UPDATE
            ],
            'Date Of Modification'
        )->addForeignKey(
            $installer->getFkName(
                RevisionHistoryResource::MAIN_TABLE_NAME,
                'revision_id',
                RevisionResource::MAIN_TABLE_NAME,
                'revision_id'
            ),
            'revision_id',
            $installer->getTable(RevisionResource::MAIN_TABLE_NAME),
            'revision_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($revisionHistoryTable);

        $setup->endSetup();
    }
}
