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
 
namespace Ced\CsMarketplace\Block\Vendor;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	protected $urlModel;

	protected $session;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,		
		UrlFactory $urlFactory
    ){
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
        $this->session = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession();
        parent::__construct($context);
    }

	
	/**
     * Retrieve customer session model object
     *
     * @return Session
     */
    protected function _getSession(){
        return $this->session;
    }
	
	/**
     * Get vendor ID
     *
     * @return int
     */
	public function getVendorId() {
		return $this->session->getVendorId();
	}
	
	/**
     * Get vendor
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    public function getVendor() {
		return $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($this->getVendorId());		
    }
	
	/**
     * Get customer
     *
     * @return Mage_Customer_Model_Customer
     */
	public function getCustomer(){
		return $this->_objectManager->get('Magento\Customer\Model\Customer')->load($this->getCustomerId());
    }
	
	/**
     * Get customer ID
     *
     * @return int
     */
	public function getCustomerId() {
		return $this->session->getCustomerId();
	}
	/**
     * Get object Manager
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager() {
        return $this->_objectManager;
    }
	/**
     * Get vendor url in vendor dashboard
     *
     * @return string
     */
    public function getVendorUrl() {
		return  $this->urlModel->create()->getUrl('csmarketplace/vendor/edit', ['_secure' => true]);
    }

    /**
     * Get back url in vendor dashboard
     *
     * @return string
     */
    public function getBackUrl() {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return  $this->urlModel->create()->getUrl('csmarketplace/vendor/', ['_secure' => true]);
    }
}
