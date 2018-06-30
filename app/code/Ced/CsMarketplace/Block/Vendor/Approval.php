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

namespace Ced\CsMarketplace\Block\Vendor;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
class Approval extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

	public $_vendorUrl;

	protected $urlModel;
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Approval constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Ced\CsMarketplace\Model\Url $vendorUrl
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Ced\CsMarketplace\Model\Url $vendorUrl,
		UrlFactory $urlFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
		$this->_vendorUrl = $vendorUrl;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
		parent::__construct($context, $customerSession, $objectManager, $urlFactory);
	}
	
	
	
	/**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_vendorUrl->getBaseUrl();
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_vendorUrl->getLogoutUrl();
    }
	
	/**
     * Approval message
     *
     * @return String
     */
	public function getApprovalMessage() {
		$message = '';
		if ($this->getVendorId()) {
			switch ($this->getVendor()->getStatus()) {
				case \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS : $message .= __('Your vendor account has been Disapproved.'); break;
				default : $message .= __('Your vendor account is under admin approval.'); break;
			}
		} else {
			$message .= __('You are not a vendor yet. Send request for become a vendor.');
		}
		return $message;
	}
}
