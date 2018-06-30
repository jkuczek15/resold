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

namespace Ced\CsMarketplace\Controller\Vpayments;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Ced\CsMarketplace\Model\Vpayment;
use Magento\Framework\Module\Manager;
use Ced\CsMarketplace\Helper\Payment;
use Magento\Framework\Registry;

class View extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public $_vpayment;

    public $_payment;

    public $_coreRegistry = null;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        Manager $moduleManager,
        Vpayment $vpayment,
        Payment $payment,
        Registry $registry
    ) {
        $this->_vpayment = $vpayment;
        $this->_coreRegistry = $registry;
        $this->_payment = $payment;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);

    }

    public function execute()
    {

        if(!$this->_getSession()->getVendorId()) {
            return false;
        }
        if (!$this->_loadValidPayment()) {
            return false;
        }
        
        $resultPage = $this->resultPageFactory->create();     

        
        $resultPage->getConfig()->getTitle()->set(__('Transaction Details'));
        return $resultPage;
    }

    /**
     * Check order view availability
     * @param $payment
     * @return bool
     */
    protected function _canViewPayment($payment)
    {
        if(!$this->_getSession()->getVendorId()) {
            return false;
        }
        $vendorId = $this->_getSession()->getVendorId();
        $paymentId = $payment->getId();
        
        
        $collection = $this->_vpayment->getCollection();
        $collection->addFieldToFilter('entity_id', $paymentId)
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
    protected function _loadValidPayment($paymentId = null)
    {
        if(!$this->_getSession()->getVendorId()) {
            return false;
        }
        if (null === $paymentId) {
            $paymentId = (int) $this->getRequest()->getParam('payment_id');
        }
        if (!$paymentId) {
            $this->_forward('noRoute');
            return false;
        }
        $payment = $this->_vpayment->load($paymentId);
        
        if ($this->_canViewPayment($payment)) {
            $this->_coreRegistry->register('current_vpayment', $payment);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }
    
    

    
    
    
    /**
     * Export Payment Action
     */
    public function exportCsvAction()
    {
        if(!$this->_getSession()->getVendorId()) {
            return;
        }
        $filename = 'vendor_transactions.csv';
        $content = $this->_payment->getVendorCommision();
        $this->_prepareDownloadResponse($filename, $content);

    }    
}
