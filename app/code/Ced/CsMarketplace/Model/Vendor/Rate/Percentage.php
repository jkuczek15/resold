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

namespace Ced\CsMarketplace\Model\Vendor\Rate;

use Magento\Framework\Api\AttributeValueFactory;

class Percentage extends Abstractrate
{
    
    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    protected $extensionAttributesFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesInterface
     */
    protected $extensionAttributes;

    /**
     * @var AttributeValueFactory
     */
    protected $customAttributeFactory;

    /**
     * @var string[]
     */
    protected $customAttributesCodes = null;

    /**
     * @var bool
     */
    protected $customAttributesChanged = false;
    
    public $_objectManager;

    /**
     * @param \Magento\Framework\Model\Context                  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory                             $customAttributeFactory
     * @param ResourceModel\Vendor                              $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb     $resourceCollection
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory,
        \Magento\Framework\Api\AttributeValueFactory $attributeValueFactory,
        array $data = []
    ) {

        $this->_objectManager = $objectInterface;
        parent::__construct(
            $context,
            $registry,
            $extensionAttributesFactory,
            $attributeValueFactory
        );
    }
    /**
     * Get the commission based on group
     *
     * @param  float  $grand_total
     * @param  float  $base_grand_total
     * @param  string $currency
     * @param  array  $commissionSetting
     * @return array
     */
    public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array()) 
    {
        $result = array();
        
        $order = $this->getOrder();
        $commissionSetting['rate'] = min($commissionSetting['rate'], 100);
        $base_fee = ( $commissionSetting['rate'] * $base_grand_total ) / 100 ;

        
        //	$base_fee = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreId())->roundPrice($base_fee);
        $base_fee = round($base_fee, 2);
        
        $result['base_fee'] = max($base_fee, 0);
        $fee = (floatval($commissionSetting['rate']) * floatval($grand_total)) / 100 ;
        //$fee = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreId())->roundPrice($fee);
        
        //	$fee = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreId())->roundPrice($fee); 
        $fee= round($fee, 2);
        $result['fee'] = max($fee, 0);
        
        $itemCommission = isset($commissionSetting['item_commission']) ? $commissionSetting['item_commission'] : array();
        if(count($itemCommission) > 0) {
            unset($commissionSetting['item_commission']);
            $item_commission = array();
            foreach($itemCommission as $itemId=>$base_price) {
                $price =$this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($order->getGlobalCurrencyCode(), false, 2, null, $order->getCurrency());
                $item_commission[$itemId] = $this->calculateCommission($price, $base_price, $base_to_global_rate, $commissionSetting);
            }
            $result['item_commission'] = json_encode($item_commission);
        }

        return $result;
    }
    

    

}
