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
 * @package   Ced_CsStripePayment
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStripePayment\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		$this->_objectManager = $objectManager;
		parent::__construct($context);
		$this->urlBuilder = $context->getUrlBuilder();
		$this->_helper = $this->_objectManager->get('Ced\CsStripePayment\Helper\Data');
	}
	
	public function saveValues($account,$vendorId)
	{

		$custStripeData = $this->_objectManager->create('Ced\CsStripePayment\Model\Managed');
		$custStripeData->setData('vendor_id',$vendorId);
		$custStripeData->setData('account_id',$account->id);
		$custStripeData->setData('email_id',"devcedcommerce@gmail.com");
		$custStripeData->setData('secret_key',$account->keys->secret);
		$custStripeData->setData('publishable_key',$account->keys->publishable);
		$custStripeData->save();
		return;
	}

	public function getSessionQuote()
	{
		/* if (Mage::app()->getStore()->isAdmin())
		{
			return Mage::getSingleton('adminhtml/sales_order_create')->getQuote();
		} */
		return $this->_objectManager->create('Magento\Checkout\Model\Session')->getCustomer()->getQuote();
	}
	
	public function getCustomerEmail()
	{
		return $this->getSessionQuote()->getCustomerEmail();
	}
	
	public function getOnStore()
	{
		if (Mage::app()->getStore()->isAdmin())
		{
			try
			{
				if (Mage::app()->getRequest()->getParam('order_id'))
				{
					$orderId = Mage::app()->getRequest()->getParam('order_id');
					$order = Mage::getModel('sales/order')->load($orderId);
					$store = $order->getStore();
				}
				elseif (Mage::app()->getRequest()->getParam('invoice_id'))
				{
					$invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
					$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
					$store = $invoice->getStore();
				}
				elseif (Mage::app()->getRequest()->getParam('creditmemo_id'))
				{
					$creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id');
					$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
					$store = $creditmemo->getStore();
				}
	
				if (!empty($store) && $store->getId())
					return $store;
			}
			catch (Exception $e) {}
		}
		return Mage::app()->getStore();
	}
	/*
	 * @return Magento CustomerId
	*/
	public function getCustomerId()
	{
	/* 	if (Mage::app()->getStore()->isAdmin())
		{
			return Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getCustomerId();
		}
		else if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			return Mage::getSingleton('customer/session')->getCustomer()->getId();
		} */
		
		return $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomer()->getId();
		return null;
	}
	public function isGuest()
	{
		return $this->getSessionQuote()->getCheckoutMethod() == 'guest';
	}
	public function isZeroDecimal($currency)
	{
		return in_array(strtolower($currency), array(
				'bif', 'djf', 'jpy', 'krw', 'pyg', 'vnd', 'xaf',
				'xpf', 'clp', 'gnf', 'kmf', 'mga', 'rwf', 'vuv', 'xof'));
	}
	public function log($msg)
	{
		Mage::log("Stripe Payments - ".$msg);
	}
	public function getMultiCurrencyAmount($payment, $baseAmount)
	{
		$order = $payment->getOrder();
		$grandTotal = $order->getGrandTotal();
		$baseGrandTotal = $order->getBaseGrandTotal();
		$rate = $order->getStoreToOrderRate();
		if ($baseAmount == $baseGrandTotal)
			return $grandTotal;
		else if (is_numeric($rate))
			return min($baseAmount * $rate, $grandTotal);
		else
			return $baseAmount;
	}
	/*
	 * Update last retrievd using $stripeCustomerId
	*/
	public function updateLastRetrieved($stripeCustomerId)
	{
		try
		{
			$cusStripeId = $this->_objectManager->create('Ced\CsStripePayment\Model\Customer')->load($stripeCustomerId,'stripe_id')->getId();
			$fields = array();
			$fields['last_retrieved'] = time();
			$this->_objectManager->create('Ced\CsStripePayment\Model\Customer')->load($cusStripeId)->setLastRetrieved($fields['last_retrieved'])->save();
		}
		catch (Exception $e)
		{
			Mage::helper('csstripepayment')->log($this->exception('Could not update Stripe customers table: '.$e->getMessage()));
		}
	}
	/*
	 * @return exception
	*/
	public function exception($str) {
		return Mage::helper('csstripepayment')->__($str);
	}
	
	
	
	
	
}