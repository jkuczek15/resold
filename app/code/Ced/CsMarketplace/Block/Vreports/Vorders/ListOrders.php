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

namespace Ced\CsMarketplace\Block\Vreports\Vorders;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class ListOrders extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	protected $_filterCollection;
	public $_vendorUrl;

	protected $urlModel;
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	/**
	 * Set the Vendor object and Vendor Id in customer session
	 */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Ced\CsMarketplace\Model\Url $vendorUrl,
		UrlFactory $urlFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
		parent::__construct($context, $customerSession, $objectManager, $urlFactory);
		$this->_vendorUrl = $vendorUrl;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
		$ordersCollection = [];
	    $reportHelper =	$this->_objectManager->get('Ced\CsMarketplace\Helper\Report');
		$params = $this->session->getData('vorders_reports_filter');
		
		if (isset($params) && $params != null) {
			$ordersCollection = $reportHelper->getVordersReportModel($this->getVendor(), $params['period'], $params['from'], $params['to'], $params['payment_state']);
	
			if(count($ordersCollection) > 0){
				$this->_filtercollection = $ordersCollection;
				$this->setVordersReports($this->_filtercollection);
			}
		}
	}
		
}
		
