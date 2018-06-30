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

namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class MassDelete extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{


    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $vendorIds = $this->getRequest()->getParam('vendor_id');
        if (!is_array($vendorIds)) {
            $this->messageManager->addErrorMessage(__('Please select vendor(s).'));
        } else {
            if (!empty($vendorIds)) {
                try {
                    foreach ($vendorIds as $vendorId) {
                        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
                        $this->_eventManager->dispatch('csmarketplace_controller_adminhtml_vendor_delete', array('vendor' => $vendor));
                        $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->deleteVendorProducts($vendorId);
                        $this->_objectManager->create('Ced\CsMarketplace\Helper\Mail')
                                ->sendAccountEmail(\Ced\CsMarketplace\Model\Vendor::VENDOR_DELETED_STATUS, '', $vendor);
                        $vendor->delete();
                    }
                    $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been deleted.', count($vendorIds)));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
        return $this->_redirect('*/*/index');

    }
}
