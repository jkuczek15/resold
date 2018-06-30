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

namespace Ced\CsMarketplace\Block\Vpayments\ListBlock;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class Request extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	public $_objectManager;
	protected $urlModel;
	public $_acl;
	public $_localeCurrency;
	protected $_session;
	protected $_csMarketplaceHelper;

    /**
     * Request constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     * @param \Ced\CsMarketplace\Helper\Acl $acl
     * @param \Ced\CsMarketplace\Helper\Data $csMarketplaceHelper
     */
	public function __construct(Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		UrlFactory $urlFactory,
		\Magento\Framework\Locale\Currency $localeCurrency,
		\Ced\CsMarketplace\Helper\Acl $acl,
		\Ced\CsMarketplace\Helper\Data $csMarketplaceHelper){
			$this->_session = $customerSession;
			$this->urlModel = $urlFactory;
			$this->_objectManager = $objectManager;
			$this->_acl = $acl;
			$this->_localeCurrency = $localeCurrency;
			$this->_csMarketplaceHelper=  $csMarketplaceHelper;
			parent::__construct($context, $customerSession, $objectManager, $urlFactory);
		$pendingPayments = array();
		if ($vendorId = $this->getVendorId()) {
			$pendingPayments = $this->getVendor()->getAssociatedOrders()
									->addFieldToFilter('order_payment_state', array('in'=>[\Magento\Sales\Model\Order\Invoice::STATE_PAID, \Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID]))
									->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_OPEN))
									->setOrder('created_at', 'ASC');
			$main_table=$this->_csMarketplaceHelper->getTableKey('main_table');
			$order_total=$this->_csMarketplaceHelper->getTableKey('order_total');
			$shop_commission_fee=$this->_csMarketplaceHelper->getTableKey('shop_commission_fee');
			$pendingPayments->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
			
			$pendingPayments = $this->filterPayment($pendingPayments);
		}
		$this->setPendingVpayments($pendingPayments);
	}

    /**
     * @param $payment
     * @return mixed
     */
	public function filterPayment($payment){	
		$params = $this->_session->getData('payment_request_filter');
		if(is_array($params) && count($params)>0){
			foreach($params as $field=>$value){
				if($field=="__SID")
					continue;
				if(is_array($value)){
					if(isset($value['from']) && urldecode($value['from'])!=""){
						$from = urldecode($value['from']);					
						if($field=='created_at'){
							$from=date("Y-m-d 00:00:00",strtotime($from));
						} 
						
						$payment->addFieldToFilter($field, array('gteq'=>$from));
					}
					if(isset($value['to'])  && urldecode($value['to'])!=""){
						$to = urldecode($value['to']);					
						if($field=='created_at'){
							$to=date("Y-m-d 59:59:59",strtotime($to));
						}
						
						$payment->addFieldToFilter($field, array('lteq'=>$to));
					}
				}else if(urldecode($value)!=""){
					if($field == 'payment_method') {
						$payment->addFieldToFilter($field, array("in"=>$this->_acl->getDefaultPaymentTypeValue(urldecode($value))));
					} else {
						$payment->addFieldToFilter($field, array("like"=>'%'.urldecode($value).'%'));
					}
				}
			
			}
		}
		return $payment;		
	}

	public function cancelledTransaction(){
		if ($vendorId = $this->getVendorId()) {
			$requested=$this->_objectManager->create('Ced\CsMarketplace\Model\Vpayment\Requested')->getCollection()->addFieldToFilter('vendor_id', $vendorId)->addFieldToFilter('status', array('neq'=>\Ced\CsMarketplace\Model\Vpayment\Requested::PAYMENT_STATUS_REQUESTED))->getData();
			return $requested;
		}else{
		    return false;
        }
	}

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Html\Pager', 'custom.pager');
		$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
		$pager->setCollection($this->getPendingVpayments());
		$this->setChild('pager', $pager);
		$this->getPendingVpayments()->load();
		return $this;
	}
	/**
	 * return the pager
	 *
	 */
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}
	
	/**
	 * return Back Url
	 *
	 */
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',['_secure'=>true,'_nosid'=>true]);
	}
	
}
