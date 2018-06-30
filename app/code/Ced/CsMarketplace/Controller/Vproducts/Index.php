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

namespace Ced\CsMarketplace\Controller\Vproducts;

class Index extends \Ced\CsMarketplace\Controller\Vproducts
{
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) { 
            return false;
        }
        $params = $this->getRequest()->getParams();
        if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ) {
            $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->setData('product_filter', $params); 
        } 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Product List'));
        return $resultPage;
    }
}
