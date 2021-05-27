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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Plugin;

use Magento\Store\Model\ScopeInterface;

class SetVendorPanel
{
    
    public $_scopeConfig;
    
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
	{
	    $this->_scopeConfig = $scopeConfig;
	}
		
    public function afterGetConfigurationDesignTheme($subject, $result) 
    {
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Registry');
        if($registry->registry('vendorPanel')){
            $result = $this->_scopeConfig->getValue('ced_csmarketplace/general/theme', ScopeInterface::SCOPE_STORE);
        }
        return $result;
    }
}