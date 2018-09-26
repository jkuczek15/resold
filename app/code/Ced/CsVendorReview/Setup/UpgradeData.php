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
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

     
namespace Ced\CsVendorReview\Setup;
 
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $tableName = $setup->getTable('ced_csvendorreview_rating');
            
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $data = [
                [
                'rating_label' => 'Quality',
                'rating_code' => 'quality',
                'sort_order' => 1,
                ],
                [
                'rating_label' => 'Pricing',
                'rating_code' => 'pricing',
                'sort_order' => 2,
                ],
                [
                'rating_label' => 'Service',
                'rating_code' => 'service',
                'sort_order' => 3,
                ]
                ];
 
                
                foreach ($data as $item) {
                    $setup->getConnection()->insert($tableName, $item);
                }
            }
        }
 
        $setup->endSetup();
    }
}
