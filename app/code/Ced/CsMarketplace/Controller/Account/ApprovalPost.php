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

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\Module\Manager;
use Ced\CsMarketplace\Model\Vendor;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApprovalPost extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
* 
     *
 * @var AccountManagementInterface 
*/
    protected $customerAccountManagement;

    /**
* 
     *
 * @var CustomerRepositoryInterface  
*/
    protected $customerRepository;

    /**
* 
     *
 * @var Validator 
*/
    protected $formKeyValidator;

    /**
* 
     *
 * @var CustomerExtractor 
*/
    protected $customerExtractor;
    public $_vendor;

    /**
     * @param Context                     $context
     * @param Session                     $customerSession
     * @param PageFactory                 $resultPageFactory
     * @param AccountManagementInterface  $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator                   $formKeyValidator
     * @param CustomerExtractor           $customerExtractor
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator,
        CustomerExtractor $customerExtractor,
        UrlFactory $urlFactory,
        Manager $moduleManager,
        Vendor $Vendor
     ) {
        $this->_vendor = $Vendor;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerExtractor = $customerExtractor;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    /**
     * Change customer password action
     *
     * @return                                       \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if($this->getRequest()->getParam('is_vendor')==1) {
            $venderData = $this->getRequest()->getParam('vendor');
            $customerData = $this->_getSession()->getCustomer();
            
            try {
                $vData= $this->_vendor->getCollection()->addAttributeToFilter('shop_url', $venderData['shop_url'] )->getData();
                if(sizeof($vData)>0){
                    $this->messageManager->addErrorMessage(__('Shop url already exist. Please Provide another Shop Url'));
                    return $resultRedirect->setPath('seller/sell/form');
                }
                $vendor =  $this->_vendor->setCustomer($customerData)->register($venderData);
                
                if(!$vendor->getErrors()) {
                    
                    $vendor->save();
                    if($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS) {
                        $this->messageManager->addSuccessMessage(__('Your vendor application has been Pending.'));
                        $resultRedirect->setPath('seller/sell/form');
                    } else if ($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
                        $this->messageManager->addSuccessMessage(__('Your vendor application has been Approved.'));
                        $resultRedirect->setPath('seller/sell/form');
                    }
                } elseif ($vendor->getErrors()) {
                    foreach ($vendor->getErrors() as $error) {
                        $this->messageManager->addErrorMessage($error);
                    }
                    $this->_getSession()->setFormData($venderData);
                } else {
                    $this->messageManager->addErrorMessage(__('Your vendor application has been denied'));
                }
                
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }    
        }
        
        return $resultRedirect;
    }
}
