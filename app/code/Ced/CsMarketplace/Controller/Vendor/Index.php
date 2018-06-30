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
 
namespace Ced\CsMarketplace\Controller\Vendor;

class Index extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {    
    	//$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->setNotification(['action'=>'yes']);
        /**
* 
         *
 * @var \Magento\Framework\View\Result\Page $resultPage 
*/
        $resultPage = $this->resultPageFactory->create();   
        $resultPage->getConfig()->getTitle()->set(__('Dashboard'));
        return $resultPage;
    }
}
