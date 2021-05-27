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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;

Class SetRtl implements ObserverInterface
{

    protected $_scopeConfig;
    private $_pageConfig;
    
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {    
        $this->_scopeConfig = $scopeConfig;
        $this->_pageConfig = $pageConfig;
    }
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        if ($this->_scopeConfig->getValue('ced_csmarketplace/general/rtl_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            
            $this->_pageConfig->addBodyClass('rtl-is-active');
        }
    }
}
