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
 * @package   Ced_CsStripePayment
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStripePayment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/*
 * Creating required Tables
 * */
class InstallSchema implements InstallSchemaInterface
{
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->run("
        		
      CREATE TABLE {$installer->getTable('ced_csstripe_managed_acc')} (
	`id` int(11) NOT NULL auto_increment,
	`vendor_id` int,
	`account_id` varchar(255),
	`email_id` varchar(255) NOT NULL default 'no',
	`secret_key` varchar(255) NOT NULL default 'no',
	`publishable_key` varchar(255) NOT NULL default 'no',
	`administrator_api_key` varchar(255) NOT NULL default 'no',
	 PRIMARY KEY  (`id`)
           )ENGINE=InnoDB DEFAULT CHARSET=utf8;
        		");
        
        $installer->run("
        CREATE TABLE {$installer->getTable('ced_csstripe_standalone_acc')} ( 
        `id` int(11) NOT NULL,
        `access_token` text,
        `refresh_token` varchar(255) DEFAULT NULL,
        `token_type` varchar(255) NOT NULL DEFAULT 'no',
        `stripe_publishable_key` varchar(255) NOT NULL DEFAULT 'no',
        `stripe_user_id` varchar(255) NOT NULL DEFAULT 'no',
        `scope` varchar(255) NOT NULL DEFAULT 'no',
        `vendor_id` int(11) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        
        $installer->endSetup();

    }
}
