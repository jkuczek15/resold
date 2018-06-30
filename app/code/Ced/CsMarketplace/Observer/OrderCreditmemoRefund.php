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

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;

Class OrderCreditmemoRefund implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }
    /**
     * Refund the asscociated vendor order
     *
     * @param  Varien_Object $observer
     * @return Ced_CsMarketplace_Model_Observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getDataObject();
        try {
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_CLOSED) {
                $vorders = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getCollection()
                    ->addFieldToFilter('order_id', array('eq'=>$order->getIncrementId())); 
                if (count($vorders) > 0) {
                    foreach ($vorders as $vorder) {
                        if($vorder->canCancel()) {
                            $vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_CANCELED);
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED);
                            $vorder->save();
                        } else if($vorder->canMakeRefund()) {
                            $vorder->setPaymentState(\Ced\CsMarketplace\Model\Vorders::STATE_REFUND);
                            $vorder->save();
                        }
                    }
                }
            }
            return $this;
        } catch(\Exception $e) {
            $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->logException($e);
        }
        return $this;
    }
}    
