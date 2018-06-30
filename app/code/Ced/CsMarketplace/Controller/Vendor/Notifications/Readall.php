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
 
namespace Ced\CsMarketplace\Controller\Vendor\Notifications;

class Readall extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {    

         $result = [];
        if (!$this->_getSession()->getVendorId()) {
            return $this->_redirect('csmarketplace/vendor/index');
        }
        else
        {
            $vendorId = $this->_getSession()->getVendorId();
            $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->readAllNotifications($vendorId);
            //$this->messageManager->addSuccessMessage(__('Notifications.'));
            return $this->_redirect('csmarketplace/vendor/index');
        }

        
    }
}

