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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model\System\Config\Source\Vproducts;

use Magento\Store\Model\StoreManagerInterface;

class Type extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Supported Product Type by Ced_CsMarketplace extension.
     */     
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_TYPE = 'ced_csmarketplace/vproducts/types';
    
    protected $_context;
    protected $_storeManger;
    protected $_producttype;
 
    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Type $producttype,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->_context = $context;
        $this->_producttype = $producttype;
    }
    
    /**
     * Retrieve Option values array
     *
     * @param  boolean $defaultValues
     * @param  boolean $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false, $storeId=null)
    {
        $options = [];
        if (!$defaultValues) {
            if ($storeId == null) { 
                $stores = $this->storeManager->getStores(false, true); 
            }
            $storeId = current($stores)->getId();
            $allowedType = $this->getAllowedType($storeId);
        }
        
        $types = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_TYPE);
        $types = array_keys((array)$types);
       
        foreach($this->_producttype->getOptionArray() as $value=>$label) {
            if (in_array($value, $types)) {
               if (!$defaultValues && !in_array($value, $allowedType)) {
                    continue;
                }
                $options[] = array('value'=>$value,'label'=>$label);
            }
        }
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
        return $options;
    }
    
    /**
     * Get Allowed product type
     *
     * @param  int $storeId
     * @return array
     */
    public function getAllowedType($storeId = 0) 
    {
        if ($storeId) {
            if($this->_context->getRequest()->getParam('store'))
                return explode(',', $this->_context->getScopeConfig()->getValue('ced_vproducts/general/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_context->getRequest()->getParam('store')));
            return explode(',', $this->_context->getScopeConfig()->getValue('ced_vproducts/general/type')/* ,$storeId */);
        }
        return explode(',', $this->_context->getScopeConfig()->getValue('ced_vproducts/general/type'));
    } 
}
