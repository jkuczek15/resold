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

namespace Ced\CsMarketplace\Block\Vorders\View;


class Info extends \Magento\Sales\Block\Order\Info
{
	public $_objectManager;
    /**
     * Info constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		parent::__construct($context, $registry, $paymentHelper,$addressRenderer );
		$this->_objectManager = $objectManager;
	}
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('vorders/view/info.phtml');
    }

    public function getLinks()
    {
        $this->checkLinks();
		 unset($this->_links['invoice']);
        return $this->_links;
    }

 	private function checkLinks()
    {
        $order = $this->getOrder();
        if (!$order->hasInvoices()) {
            unset($this->_links['invoice']);
        }
        if (!$order->hasShipments()) {
            unset($this->_links['shipment']);
        }
        if (!$order->hasCreditmemos()) {
            unset($this->_links['creditmemo']);
        }
    }
    
    public function getOrderStoreName()
    {
		
    	if ($this->getOrder()) {
    		$storeId = $this->getOrder()->getStoreId();
    		if (is_null($storeId)) {
    			$deleted = __(' [deleted]');
    			return nl2br($this->getOrder()->getStoreName()) . $deleted;
    		}

    		$store =$this->_storeManager->getStore($storeId);
    		$name = array(
    				$store->getWebsite()->getName(),
    				$store->getGroup()->getName(),
    				$store->getName()
    		);
    		return implode('<br/>', $name);
    	}
    	return null;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Order # %1', $this->getOrder()->getRealOrderId()));
        $infoBlock = $this->paymentHelper->getInfoBlock($this->getOrder()->getPayment(), $this->getLayout());
        $this->setChild('payment_info', $infoBlock);
    }
}
