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

namespace Ced\CsMarketplace\Controller\Vproducts;

class Delete extends \Ced\CsMarketplace\Controller\Vproducts
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
 
    public function execute()
    {
        if (!$this->_getSession()->getVendorId()) { 
            return; 
        }
        $id = $this->getRequest()->getParam('id');
        $vendorId = $this->_getSession()->getVendorId();
        $redirectBack = false;
        $vendorProduct = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->isAssociatedProduct($vendorId, $id);
        if (!$vendorProduct) {
            $redirectBack = true;
        } elseif ($id) {
            $this->_objectManager->get('Magento\Framework\Registry')->register("isSecureArea", 1);
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            try {
                $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
                if($product && $product->getId()) {
                    $product->delete();
                }
                $this->messageManager->addSuccessMessage(__('Your Product Has Been Sucessfully Deleted'));
            } catch (\Exception $e) {
            	$redirectBack = true;
            }
        } else { 
            $redirectBack = true; 
        }    
        if ($redirectBack) {
            $this->messageManager->addError(__('Unable to delete the product'));                
        }
        $this->_redirect('*/*/index');    
    }
}
