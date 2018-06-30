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

namespace Ced\CsMarketplace\Controller\Vproducts;

class Edit extends \Ced\CsMarketplace\Controller\Vproducts
{
   
    public function execute()
    {
        if (!$this->_getSession()->getVendorId()) { 
            return; 
        }
        $id = $this->getRequest()->getParam('id');
        $vendorId = $this->_getSession()->getVendorId();
        $vendorProduct = 0;
        if ($id && $vendorId) {
            $vendorProduct = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                            ->isAssociatedProduct($vendorId, $id);
        }
        
        if (!$vendorProduct) {
            $this->_redirect('csmarketplace/vproducts/index');
            return;
        }
        
        if ($type = $this->getRequest()->getParam('type')) {
            $resultPage = $this->resultPageFactory->create();
            $update = $resultPage->getLayout()->getUpdate();
            $update->addHandle('default');
            switch ($type){
                case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE : $update->addHandle('csmarketplace_vproducts_simple');
                    break;
                case \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL : $update->addHandle('csmarketplace_vproducts_virtual');
                    break;
                case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE : $update->addHandle('csmarketplace_vproducts_downloadable');
                    break;
                default:$this->_redirect('csmarketplace/vproducts/index');
                    break;
            }
            $this->_view->renderLayout();
        } else {
            $this->_redirect('csmarketplace/vproducts/index');
        }    
    }
}
