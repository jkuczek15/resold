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
 
namespace Ced\CsMarketplace\Model\Vendor\Payment\Methods;
 
class AbstractModel extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = '';
    protected $_fields = array();
    protected $_codeSeparator = '-';
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
     * Get the code
     *
     * @return string
     */
    public function getCode() 
    {
        return $this->_code;
    }
    
    /**
     * Get the code separator
     *
     * @return string
     */
    public function getCodeSeparator() 
    {
        return $this->_codeSeparator;
    }
    
    /**
     *  Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $this->_fields = array();
        $this->_fields['active'] = ['type'=>'select',
                                    'values'=>['0'=> __('No'),'1'=>__('Yes')]
                                    ];
        return $this->_fields;
    }
    
    /**
     * Retreive labels
     *
     * @param  string $key
     * @return string
     */
    public function getLabel($key) 
    {
        switch($key) {
        case 'active' : 
            return __('Active'); break;
        default : 
            return __($key); break;
        }
    }
}
