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
 
namespace Ced\CsMarketplace\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{

    protected $_formKeyValidator;
    protected $_strUtil;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);   
        $this->_formKeyValidator = $formKeyValidator;
    }
    
    /**
     * Default vendor profile page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) {
            return $this->_redirect('*/account/login');
        }
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/profile');
        }
        if ($data = $this->getRequest()->getPost()) {
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');
            $this->_objectManager->get('\Magento\Framework\Registry')->register('data_com', $this->getRequest()->getParam('vendor_id'));
            if($id = $this->_getSession()->getVendorId()) {
                $model->load($id);
                if(isset($data['vendor'])) {
                    $model->addData($data['vendor']);
                    try {
                        if($model->validate()) {
                            $model->extractNonEditableData();
                            $model->save();
                            $customer = $this->_getSession()->getCustomer();
                            $dataPost = $this->getRequest()->getParams();
                            $vendorData = $dataPost['vendor'];
                            if(array_key_exists('current_password', $vendorData)){
                                $currPass   = $dataPost['vendor']['current_password'];
                                $newPass    = $dataPost['vendor']['new_password'];
                                $confPass   = $dataPost['vendor']['confirm_password'];

                                $oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
                                /*if ($this->_strUtil->strpos($oldPass, ':')) {
                                    list($_salt, $salt) = explode(':', $oldPass);
                                } else {
                                    $salt = false;
                                }*/
                                list($_salt, $salt) = explode(':', $oldPass);
                                if ($customer->hashPassword($currPass, $salt) == $oldPass) {
                                    if (strlen($newPass)) {
                                        $customer->setPassword($newPass);
                                        $customer->setPasswordConfirmation($confPass);
                                        $customer->save();
                                    } else {
                                        $this->messageManager->addError(__('New password field cannot be empty.'));
                                    }
                                } else {
                                    $this->messageManager->addError(__('Invalid current password'));
                                }
                            }
                        } elseif ($model->getErrors()) {
                            foreach ($model->getErrors() as $error) {
                                $this->messageManager->addErrorMessage($error);
                            }
                            $this->_getSession()->setFormData($data);
                            return $this->_redirect('*/*/profile');
                        }
                        $this->_getSession()->setVendor($model->getData());
                        $this->messageManager->addSuccessMessage(__('The profile information has been saved.'));
                        return $this->_redirect('*/*/profileview');
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        return $this->_redirect('*/*/profile');
                    }
                }
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find vendor to save'));
       return $this->_redirect('*/*/profile');
       
    }
}
