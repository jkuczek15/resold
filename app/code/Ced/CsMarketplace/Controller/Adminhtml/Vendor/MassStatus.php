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


class MassStatus extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry         $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }
        

    /**
     * Promo quote edit action
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $inline = $this->getRequest()->getParam('inline', 0);
        $vendorIds = $this->getRequest()->getParam('vendor_id');
        $status = $this->getRequest()->getParam('status', '');

        if ($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
             $shop_disable = 1;     
        } 
        if ($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS) {
            $shop_disable = 2;
        }
      
        if($inline) {
            $vendorIds = array($vendorIds);
        }
        if (!is_array($vendorIds)) {
            $this->messageManager->addErrorMessage($this->__('Please select vendor(s)'));
        } else {
            try {
                $model = $this->_validateMassStatus($vendorIds, $status);
                $model->saveMassAttribute($vendorIds, array('code'=>'status', 'value' => $status));
                $shop_model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vshop');
                $shop_model->saveShopStatus($vendorIds, $shop_disable);
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been updated.', count($vendorIds)));                
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage().' An error occurred while updating the vendor(s) status.'));
            }
        }
        $this->_redirect('*/*/index');
    }
    

    /**
     * Validate batch of vendors before theirs status will be set
     * @param array $vendorIds
     * @param $status
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _validateMassStatus(array $vendorIds, $status)
    {
        $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');
        if ($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
            if (!$model->validateMassAttribute('shop_url', $vendorIds)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Some of the processed vendors have no Shop URL value defined. Please fill it prior to performing operations on these vendors.')
                );
            }
        }
        return $model;
    }
    
}
