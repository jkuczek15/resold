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


namespace Ced\CsMarketplace\Controller;

class Vpayments extends Vendor
{
    
    /**
     * Check order view availability
     *
     * @param  Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function _canViewPayment($payment)
    {
        if(!$this->_getSession()->getVendorId()) { return false;
        }
        $vendorId = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
        $paymentId = $payment->getId();
        
        
        $collection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vpayment')->getCollection();
        $collection->addFieldToFilter('id', $paymentId)
            ->addFieldToFilter('vendor_id', $vendorId);

        if(count($collection)>0) {
            return true;
        }else{
            return false;
        }
        return false;
    }
    
    /**
     * Try to load valid order by order_id and register it
     *
     * @param  int $orderId
     * @return bool
     */
    protected function _loadValidPayment($paymentId = null)
    {
        if(!$this->_getSession()->getVendorId()) { return false;
        }
        if (null === $paymentId) {
            $paymentId = (int) $this->getRequest()->getParam('payment_id');
        }
        if (!$paymentId) {
            $this->_forward('noRoute');
            return false;
        }
        $payment = $this->_objectManager->create('Ced\CsMarketplace\Model\Vpayment')->load($paymentId);
        
        if ($this->_canViewPayment($payment)) {
            $this->_objectManager->get('Magento\Framework\Registry')->register('current_vpayment', $payment);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }    
}
