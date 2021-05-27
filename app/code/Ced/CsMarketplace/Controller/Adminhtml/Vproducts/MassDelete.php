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

namespace Ced\CsMarketplace\Controller\Adminhtml\Vproducts;

class MassDelete extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    const XML_PATH_PRODUCT_EMAIL_IDENTITY = 'ced_vproducts/general/email_identity';

    public function execute()
    {
        $ids = $this->getRequest()->getParam('entity_id');
        $checkstatus = \Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS;
        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select product(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
                    if ($product && $product->getId()) {
                       $v_id = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product->getId());
                       $vendorIds[$v_id][] = array("name"=>$product->getName(), "sku"=>$product->getSku());
                       $product->delete();
                    }
                } 
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->ProductDelete($checkstatus, $vendorIds);
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                echo $e->getMessage();die;
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}

