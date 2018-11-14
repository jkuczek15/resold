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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Setup;

use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        try {
            $connection = $setup->getConnection();

            /**
             * Version 1.2.0
             */
            if (version_compare($context->getVersion(), '1.2.0', '<')) {
                $connection->addColumn(
                    $setup->getTable('plumrocket_gdpr_consents_log'),
                    'action',
                    [
                        'type' => Table::TYPE_SMALLINT,
                        'nullable' => true,
                        'comment' => 'Action',
                    ]
                );

                $connection->update(
                    $setup->getTable('plumrocket_gdpr_consents_log'),
                    ['action' => 1],
                    ['action IS NULL']
                );
            }
        } catch (\Exception $e) {}

        $setup->endSetup();
    }
}
