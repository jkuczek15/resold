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

class Delete extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * Delete promo quote action
     *
     * @return void
     */
    public function execute()
    {
        $vendorId = (int) $this->getRequest()->getParam('vendor_id');
        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
        if ($vendor && $vendor->getId()) {
            try {
                $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->deleteVendorProducts($vendor->getId());
                $this->_objectManager->create('Ced\CsMarketplace\Helper\Mail')
                    ->sendAccountEmail(\Ced\CsMarketplace\Model\Vendor::VENDOR_DELETED_STATUS, '', $vendor);
                $vendor->delete();
                $this->messageManager->addSuccessMessage(__('The vendor has been deleted.'));
            }
            catch (\Exception $e){
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');                        
    }
}
