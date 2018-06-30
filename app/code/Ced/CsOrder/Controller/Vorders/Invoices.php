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
namespace Ced\CsOrder\Controller\Vorders;
 
class Invoices extends \Ced\CsMarketplace\Controller\Vendor
{
    
    /**
     * Grid action
     *
     * @return void
     */
    public function execute()
    { 
        $orderId = (int) $this->getRequest()->getParam('order_id', 0);
        $incrementId = 0;
        if ($orderId == 0) {
            $incrementId = (int) $this->getRequest()->getParam('increment_id', 0);
            if (!$incrementId) {
                $this->_forward('noRoute');
                return;
            }
        }
        $register = $this->_objectManager->get('Magento\Framework\Registry'); 
        if($orderId) {
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($orderId);
        }
        else if($incrementId) {
            $vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->loadByField(array('order_id','vendor_id'), array($incrementId,$vendorId));
        }
        $order = $vorder->getOrder(false, false);
        $register->register('current_order', $order);
        $register->register('sales_order', $order);
        $register->register('current_vorder', $vorder);
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
