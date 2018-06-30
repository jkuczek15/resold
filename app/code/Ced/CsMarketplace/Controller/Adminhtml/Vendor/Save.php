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

namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class Save extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * Promo quote save action
     *
     * @return                                       void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');
            $registry =  $this->_objectManager->create('Magento\Framework\Registry');
            $customerid = isset($data['vendor']['customer_id']) && (int)$data['vendor']['customer_id'] ? (int)$data['vendor']['customer_id']:0;
            if($id = $this->getRequest()->getParam('vendor_id')) {
              $this->_objectManager->get('\Magento\Framework\Registry')->register('data_com', $this->getRequest()->getParam('vendor_id'));
                if($id = $this->getRequest()->getParam('vendor_id')) {

                     if( $registry->registry('ven_id')){
                         $registry->unregister('ven_id');}

                      $registry->register('ven_id',$id);
                }
             

                $model->load($id);
                if($model && $model->getId()) {
                    $customerId = (int)$model->getCustomerId();
                    if(isset($data['vendor']['customer_id'])) { unset($data['vendor']['customer_id']); 
                    }
                }
            }
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerid);
            if ($customer && $customer->getId()) {
                $data['vendor']['email'] = $customer->getEmail();
            }

            $vendorData = array_merge($this->_objectManager->create('Ced\CsMarketplace\Helper\Acl')->getDefultAclValues(), $data['vendor']);

            $model->addData($vendorData);
            
            try {
                if ($model->validate()) {
                    $this->_eventManager->dispatch('ced_csmarketplace_custom_vendor_save', [
                            'current' => $this,
                            'action'  => $this,
                    ]);
                    $model->save();
                } elseif ($model->getErrors()) {
                    foreach ($model->getErrors() as $error) {
                        $this->messageManager->addError($error);
                    }
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('vendor_id' => $model->getId()));
                    return;
                }
                $this->messageManager->addSuccessMessage(__('Vendor is successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('vendor_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id')));
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find vendor to save'));
        $this->_redirect('*/*/');            
        
    }
}
