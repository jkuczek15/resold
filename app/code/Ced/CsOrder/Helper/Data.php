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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsOrder\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $_isSplitOrder = false;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\View\Asset\Repository $assetRepo  
    ) {
    
        $this->_objectManager = $objectManager;
        parent::__construct($context);
        $this->_assetRepo = $assetRepo; 
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_configValueManager = $this->_objectManager->get('Magento\Framework\App\Config\ValueInterface');
        $this->_transaction = $this->_objectManager->get('Magento\Framework\DB\Transaction');
    }

    /**
     * @param $address
     * @return bool|string
     */
    public function getVendorNameByAddress($address) 
    {
        
        if (is_numeric($address)) {
              $address=$this->_objectManager->get('Magento\Customer\Model\Address')->load($address);
            if($address->getVendorId()) {
                $vendor=$this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($address->getVendorId());
                return $vendor->getName();
            }
            else
            {
                return 'Admin';
            }
        } elseif ($address && $address->getId()) {
            $vendor=$this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($address->getVendorId());
            return $vendor->getName();
        }else{
            return false;
        }
        
    }
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorLogEnabled()
    {
        return $this->_scopeConfigManager->getValue('ced_csmarketplace/vlogs/active', $this->getStore()->getId());
    }

    
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore() 
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if($storeId) {
            return $this->_scopeConfigManager->getStore($storeId); 
        }
        else { 
            return $this->_scopeConfigManager->getStore(); 
        }
    }

    /**
     * @param $data
     * @param bool $tag
     */
    public function logProcessedData($data, $tag=false) 
    {
     

        if(!$this->isVendorLogEnabled()) {
            return; 
        }

        $controller =$this->_objectManager->get('Magento\Framework\View\Context')->getControllerName();
        $action = $this->_objectManager->get('Magento\Framework\View\Context')->getActionName();
        $router = $this->_objectManager->get('Magento\Framework\View\Context')->getRouteName();
        $module = $this->_objectManager->get('Magento\Framework\View\Context')->getModuleName();
        
        $out = '';
        //if ($html) 
        @$out .= "<pre>";
        @$out .= "Controller: $controller\n";
        @$out .= "Action: $action\n";
        @$out .= "Router: $router\n";
        @$out .= "Module: $module\n";
        foreach(debug_backtrace() as $key=>$info)
        {
            @$out .= "#" . $key . " Called " . $info['function'] ." in " . $info['file'] . " on line " . $info['line']."\n"; 
            break;        
        }
        if($tag) {
            @$out .= "#Tag " . $tag."\n"; 
        } 
            
        //if ($html)
        @$out .= "</pre>";
    }


    /**
     * @param Exception $e
     */
    public function logException(Exception $e) 
    {
        if(!$this->isVendorLogEnabled()) {
            return; 
        }
        
    }
     
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorDebugEnabled()
    {
        $isDebugEnable = (int)$this->_scopeConfigManager->getValue('ced_csmarketplace/vlogs/debug_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if($isDebugEnable ) {
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps =$this->_scopeConfigManager->getValue('dev/restrict/allow_ips', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if ($isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search($this->_objectManager->get('Magento\CoreModel\Http')->getHttpHost(), $allowedIps) === false
                ) {
                    $allow = false;
                }
            }
        }

        return $allow;
    
    }
    
    
    
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isSplitOrderEnabled()
    {
        $this->_isSplitOrder = (boolean)$this->_scopeConfigManager->getValue('ced_vorders/general/vorders_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $this->_isSplitOrder;
    }

    /**
     * @param $vorder
     * @return bool
     */
    public function canCreateInvoiceEnabled($vorder)
    {
        //if(!Mage::app()->getStore()->isAdmin() && $vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
        $isSplitOrderEnable = (boolean)$this->_scopeConfigManager->getValue('ced_vorders/general/vorders_caninvoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $isSplitOrderEnable;
    }

    /**
     * @param $vorder
     * @return bool
     */
    public function canCreateShipmentEnabled($vorder)
    {
        if($vorder->canShowShipmentButton()) {
            $isSplitOrderEnable = (boolean)$this->_scopeConfigManager->getValue('ced_vorders/general/vorders_canshipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            return $isSplitOrderEnable;
        }
        /*
        if($vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
        $isSplitOrderEnable = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_canshipment');
        return $isSplitOrderEnable;
        }*/
        return false;
            
    }

    /**
     * @param $vorder
     * @return bool
     */
    public function canCreateCreditmemoEnabled($vorder)
    {
        //if(!Mage::app()->getStore()->isAdmin() && $vorder->isAdvanceOrder() && $this->isSplitOrderEnabled()){
        //if(!$this->_scopeConfigManager->getStore()->isAdmin()){
        $isSplitOrderEnable = (boolean)$this->_scopeConfigManager->getValue('ced_vorders/general/vorders_cancreditmemo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $isSplitOrderEnable;
        /*}
        return false;*/    
    }
    
    
    /**
     * Check Can distribute shipment
     *
     * @return boolean
     */
    /*
    public function canEqualyDistributeShipment()
    {
		
    if(!$this->isSplitOrderEnabled()){
    $distribteShipment = (boolean)Mage::getStoreConfig('ced_vorders/general/vorders_shipment_rule');
    return $distribteShipment;
    }
    return false;
			
    }*/


    /**
     * @param $vorder
     * @return bool
     */
    public function canShowShipmentBlock($vorder)
    {
        if($vorder->getCode()==null) {
            return false; 
        }
        return true;
    }
    
    /**
     * Check Can distribute shipment
     *
     * @return boolean
     */
    
    public function isActive()
    { 
        return (boolean)$this->_scopeConfigManager->getValue('ced_vorders/general/vorders_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
