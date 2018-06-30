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

namespace Ced\CsMarketplace\Controller\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Ced\CsMarketplace\Model\Vendor;
use Ced\CsMarketplace\Helper\Data;

class Approval extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
* 
     *
 * @var CustomerRepositoryInterface  
*/
    protected $customerRepository;

    /**
* 
     *
 * @var DataObjectHelper 
*/
    protected $dataObjectHelper;

    public $vendor;

    public $helper;
    
    /**
     * @param Context                     $context
     * @param Session                     $customerSession
     * @param PageFactory                 $resultPageFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectHelper            $dataObjectHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        Vendor $vendordata,
        Data $helperdata
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->vendor = $vendordata;
        $this->helper = $helperdata;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    /**
     * Forgot customer account information page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->_getSession()->isLoggedIn() && $this->helper->authenticate($this->session->getCustomerId())) {
            $resultRedirect->setPath('seller/sell/form');
            return $resultRedirect;
        }
        
        if (!$this->authenticate($this)) { $this->_actionFlag->set('', 'no-dispatch', true); 
        }
        $this->session->unsVendorId();
        $this->session->unsVendor();
        if($this->session->isLoggedIn()) {
        
            $vendor = $this->vendor->loadByCustomerId($this->session->getCustomerId());
            if($vendor && $vendor->getId()) {
                $this->session->setData('vendor_id', $vendor->getId());
                $this->session->setData('vendor', $vendor->getData());
            }
        }
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(__('Account Approval'));
        return $resultPage;
    }
}
