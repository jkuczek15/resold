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

namespace Ced\CsMarketplace\Controller\Vorders;

class PrintAction extends \Ced\CsMarketplace\Controller\Vorders
{
    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        
        if(!$this->_getSession()->getVendorId()) {
            return $this->_redirect('*/vorders/index');

        }
        if (!$this->_loadValidOrder()) {
            return $this->_redirect('*/vorders/index');
        }

        
        $resultPage = $this->resultPageFactory->create();
        return $resultPage->addHandle('print');

    }
    
    /**
     * Check order view availability
     *
     * @param  Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _canViewOrder($vorder)
    {
        if(!$this->_getSession()->getVendorId()) { return false;
        }
        $vendorId = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
         
        $incrementId = $vorder->getOrder()->getIncrementId();
        
        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getCollection();
        $collection->addFieldToFilter('id', $vorder->getId())
            ->addFieldToFilter('order_id', $incrementId)
            ->addFieldToFilter('vendor_id', $vendorId);
        
        if(count($collection)>0) {
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * Try to load valid order by order_id and register it
     *
     * @param  int $orderId
     * @return bool
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id', 0);
        }
        $incrementId = 0;
        if ($orderId == 0) {
            $incrementId = (int) $this->getRequest()->getParam('increment_id', 0);
            if (!$incrementId) {
                $this->_forward('noRoute');
                return false;
            }
        }

        if($orderId) {
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($orderId);
        }
        else if($incrementId) {
            $vendorId = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->loadByField(array('order_id','vendor_id'), array($incrementId,$vendorId));
        }
        $order = $vorder->getOrder();
        if ($this->_canViewOrder($vorder)) {
            $this->_objectManager->get('Magento\Framework\Registry')->register('current_order', $order);
            $this->_objectManager->get('Magento\Framework\Registry')->register('current_vorder', $vorder);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }
}
