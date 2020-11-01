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

namespace Ced\CsMarketplace\Block\Vpayments;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;
class Stats extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	protected $urlModel;

	protected $session;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,		
		UrlFactory $urlFactory
    ){
		
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
        parent::__construct($context,$customerSession,$objectManager,$urlFactory);
		$this->setPendingAmount(0.00);
		$this->setPendingTransfers(0);
		$this->setPaidAmount(0.00);
		$this->setCanceledAmount(0.00);
		$this->setRefundableAmount(0.00);
		$this->setRefundedAmount(0.00);
		$this->setEarningAmount(0.00);
		
		if($this->getVendor() && $this->getVendor()->getId()) {
			$productsCollection = array();
			$paymentHelper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Payment');
			$collection=$paymentHelper->_getTransactionsStats($this->getVendor());

			if(count($collection)>0) {
				foreach ($collection as $stats){
					switch($stats->getPaymentState()) {
						case \Ced\CsMarketplace\Model\Vorders::STATE_OPEN : $this->setPendingAmount($stats->getNetAmount());
																			   $this->setPendingTransfers($stats->getCount()?$stats->getCount():0);
																			   break;
						case \Ced\CsMarketplace\Model\Vorders::STATE_PAID : $this->setPaidAmount($stats->getNetAmount());
																			   break;
						case \Ced\CsMarketplace\Model\Vorders::STATE_CANCELED : $this->setCanceledAmount($stats->getNetAmount());
																			   break;
						case \Ced\CsMarketplace\Model\Vorders::STATE_REFUND : $this->setRefundableAmount($stats->getNetAmount());
																			   break;
						case \Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED : $this->setRefundedAmount($stats->getNetAmount());
																			   break;
					}
				}
			}
			$this->setEarningAmount($this->getVendor()->getAssociatedPayments()->getFirstItem()->getBalance());
		}		
	}
	
	
}
