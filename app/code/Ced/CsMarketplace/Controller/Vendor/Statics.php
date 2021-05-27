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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Statics extends \Ced\CsMarketplace\Controller\Vendor
{

    public $resultJsonFactory;
    public $_priceCurrencyInterface;
    protected $storeManager;
    public $_localeCurrency;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        PriceCurrencyInterface $priceCurrencyInterface,
        \Magento\Framework\Locale\Currency $localeCurrency,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->_priceCurrencyInterface = $priceCurrencyInterface;
        $this->storeManager = $storeManager;
        $this->_localeCurrency = $localeCurrency;
        $this->resultJsonFactory = $resultJsonFactory;
    }


    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result = [];
        if ($vendorId = $this->_getSession()->getVendorId()) {
            $resultJson = $this->resultJsonFactory->create();
            $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $data = $this->getPendingAmount($vendor);
            $result['pendingAmount_total'] = $data['total'];
            $result['pendingAmount_action'] = $data['action'];
            $data = $this->getEarnedAmount($vendor);
            $result['earnedAmount_total'] = $data['total'];
            $result['earnedAmount_action'] = $data['action'];
            $data = $this->getOrdersPlaced($vendor);
            $result['ordersPlaced_total'] = $data['total'];
            $result['ordersPlaced_action'] = $data['action'];
            $data = $this->getProductsSold($vendor);
            $result['productsSold_total'] = $data['total'];
            $result['productsSold_action'] = $data['action'];
        }
        $resultJson->setData($result);
        return $resultJson;
    }

    public function getPendingAmount($vendor)
    {
        $data = ['total' => 0, 'action' => ''];
        if ($vendorId = $vendor->getId()) {
            $pendingAmount = 0;
           
            $ordersCollection = $this->_objectManager->get('Ced\CsMarketplace\Helper\Payment')->_getTransactionsStats($vendor);
            foreach ($ordersCollection as $order) {
                if ($order->getData('payment_state') == \Ced\CsMarketplace\Model\Vorders::STATE_OPEN) {
                    $pendingAmount = $order->getData('net_amount');
                    break;
                }
            }

            if ($pendingAmount > 1000000000000) {
                $pendingAmount = round($pendingAmount / 1000000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'T';
            } elseif ($pendingAmount > 1000000000) {
                $pendingAmount = round($pendingAmount / 1000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'B';
            } elseif ($pendingAmount > 1000000) {
                $pendingAmount = round($pendingAmount / 1000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'M';
            } elseif ($pendingAmount > 1000) {
                $pendingAmount = round($pendingAmount / 1000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($pendingAmount) . 'K';
            } else {
                $data['total'] = $this->_priceCurrencyInterface->format($pendingAmount);
            }

            $data['action'] = $this->_url->getUrl('*/vorders/', array('_secure' => true, 'order_payment_state' => 2, 'payment_state' => 1));
        }
        return $data;
    }

    /**
     * Get vendor's Earned Amount data
     * @return array
     * @throws \Zend_Currency_Exception
     */
    public function getEarnedAmount($vendor)
    {
        $data = array('total' => 0, 'action' => '');
        if ($vendor && $vendor->getId()) {
            $netAmount = $vendor->getAssociatedPayments()->getFirstItem()->getBaseBalance();
            if ($netAmount > 1000000000000) {
                $netAmount = round($netAmount / 1000000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'T';
            } elseif ($netAmount > 1000000000) {
                $netAmount = round($netAmount / 1000000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'B';
            } elseif ($netAmount > 1000000) {
                $netAmount = round($netAmount / 1000000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'M';
            } elseif ($netAmount > 1000) {
                $netAmount = round($netAmount / 1000, 4);
                $data['total'] = $this->_localeCurrency->getCurrency($this->storeManager->getStore(null)->getBaseCurrencyCode())->toCurrency($netAmount) . 'K';
            } else {
                $data['total'] = $this->_priceCurrencyInterface->format($netAmount);
            }
            $data['action'] = $this->_url->getUrl('*/vpayments/', array('_secure' => true));
        }
        return $data;
    }


    /**
     *
     * @return array
     */
    public function getOrdersPlaced($vendor)
    {
        // Total Orders Placed
        $data = array('total' => 0, 'action' => '');
        if ($vendor && $vendor->getId()) {
            $ordersCollection = $vendor->getAssociatedOrders();
            $order_total = count($ordersCollection);

            if ($order_total > 1000000000000) {
                $data['total'] = round($order_total / 1000000000000, 1) . 'T';
            } elseif ($order_total > 1000000000) {
                $data['total'] = round($order_total / 1000000000, 1) . 'B';
            } elseif ($order_total > 1000000) {
                $data['total'] = round($order_total / 1000000, 1) . 'M';
            } elseif ($order_total > 1000) {
                $data['total'] = round($order_total / 1000, 1) . 'K';
            } else {
                $data['total'] = $order_total;
            }
            $data['action'] = $this->_url->getUrl('*/vorders/', array('_secure' => true));
        }
        return $data;
    }


    /**
     * Get vendor's Products Sold data
     * @return array
     */
    public function getProductsSold($vendor)
    {
        // Total Products Sold
        $data = array('total' => 0, 'action' => '');
        if ($vendorId = $vendor->getId()) {
            $productsSold = $this->_objectManager->get('Ced\CsMarketplace\Helper\Report')
                            ->getVproductsReportModel($vendorId, '', '', false)->getFirstItem()->getData('ordered_qty');
            if ($productsSold > 1000000000000) {
                $data['total'] = round($productsSold / 1000000000000, 1) . 'T';
            } elseif ($productsSold > 1000000000) {
                $data['total'] = round($productsSold / 1000000000, 1) . 'B';
            } elseif ($productsSold > 1000000) {
                $data['total'] = round($productsSold / 1000000, 1) . 'M';
            } elseif ($productsSold > 1000) {
                $data['total'] = round($productsSold / 1000, 1) . 'K';
            } else {
                $data['total'] = round($productsSold);
            }
            $data['action'] = $this->_url->getUrl('*/vreports/vproducts', array('_secure' => true));
        }
        return $data;
    }

}
