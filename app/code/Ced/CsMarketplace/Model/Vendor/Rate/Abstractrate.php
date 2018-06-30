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
use Magento\Framework\Model\AbstractExtensibleModel;


class Abstractrate extends AbstractExtensibleModel
{
    protected $_order = null;
    protected $_vendorId = null;
    
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore() 
    {
        $storeId = (int) $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('store', 0);
        if($storeId) {
            return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeId); 
        }
        else { 
            return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null); 
        }
    }
     
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStoreId() 
    {
        return $this->getStore()->getId();
    }
     
    /**
     *  Set Order
     *
     *  @param  Ced_CsMarketplace_Model_Vorders
     *  @return Ced_CsMarketplace_Model_Vendor_Rate_Abstract
     */
    public function setOrder($order) 
    {
        $this->_order = $order;
        return $this;
    }
    
    /**
     *  Get Order
     *
     *  @return Ced_CsMarketplace_Model_Vorders
     */
    public function getOrder($order = null) 
    {
        if($this->_order == null) {
            $this->_order = $order; 
        }
        return $this->_order;
    }
    
    /**
     *  Set Order
     *
     *  @param  Ced_CsMarketplace_Model_Vorders
     *  @return Ced_CsMarketplace_Model_Vendor_Rate_Abstract
     */
    public function setVendorId($vendorId) 
    {
        $this->_vendorId = $vendorId;
        return $this;
    }
    
    /**
     *  Get Order
     *
     *  @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVendorId($vendorId = 0) 
    {
        if($this->_vendorId == null) {
            $this->_vendorId = $vendorId; 
        }
        return $this->_vendorId;
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
        return false;
    }
}
