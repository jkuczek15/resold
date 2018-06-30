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

namespace Ced\CsMarketplace\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    public $_objectManager;
    
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,EavSetupFactory $eavSetupFactory
    ) { 
        $this->_objectManager = $objectManager;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if ($context->getVersion()
            && version_compare($context->getVersion(), '2.0.0') < 0
        ) {
            
            $vendorAttribute = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor\Form')->getCollection()->addFieldToFilter('attribute_code','zip_code')->getFirstItem();
            $vendorAttribute->load($vendorAttribute->getAttributeId())->delete();

            $eavSetup->removeAttribute('csmarketplace_vendor', 'zip_code');
            
            $eavSetup->addAttribute(
                'csmarketplace_vendor',
                'zip_code',
                array(
                    'group' => 'Address Information',
                    'label' => 'Zip/Postal Code',
                    'type' => 'static',
                    'visible' => true,
                    'position' => 27,
                    'user_defined' => false,
                    'required' => true,

                )
            );
            $attribute = $eavSetup->getAttribute('csmarketplace_vendor',
                'zip_code');
            $vendorAttribute = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor\Form');
            $data = [
                'attribute_id'=>$attribute['attribute_id'],
                'attribute_code' => 'zip_code',
                'is_visible' => 1,
                'sort_order' => 27,
            ];
            $vendorAttribute->setData($data)->save();
        }
        $setup->endSetup();
    }
}
