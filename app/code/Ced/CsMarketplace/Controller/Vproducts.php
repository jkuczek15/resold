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

namespace Ced\CsMarketplace\Controller;

class Vproducts extends Vendor
{
    protected $mode='';
    const MAX_QTY_VALUE = 99999999.9999;
    /**
     * Initialize product from request parameters
     *
     * @return Magento\Catalog\Model\Product|const ERROR_IN_PRODUCT_SAVE
     */
    protected function _initProduct()
    {
        $productData=$this->getRequest()->getPost();
        $productId = $this->getRequest()->getParam('id');
    
        if ($productId) {
            $this->mode=\Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE; 
        }
        else {
            $this->mode=\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE; 
        }
    
        $productData['entity_id']= $productId;
        $errors=array();
        try{
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $product = $this->_objectManager->get('Magento\Catalog\Model\Product');
            if ($this->mode==\Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE) {
                $product->setStoreId($this->getRequest()->getParam('store_switcher', 0));
                $vendorId=$this->_getSession()->getVendorId();
                if($productId&&$vendorId) {
                    $vendorProduct = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->isAssociatedProduct($vendorId, $productId);
                    if(!$vendorProduct) {
                        return \Ced\CsMarketplace\Model\Vproducts::ERROR_IN_PRODUCT_SAVE;
                    }
                }
                $product->load($productId);
            }
            else if($this->mode==\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE) {
                $product->setStoreId(0);
                $allowedType = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type')->getAllowedType($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());
                $type = $this->getRequest()->getParam('type');
                if(!(in_array($type, $allowedType))) {
                    return \Ced\CsMarketplace\Model\Vproducts::ERROR_IN_PRODUCT_SAVE; 
                }
            }
            $product->addData(isset($productData['product'])?$productData['product']:'');
            $product->validate();
        }
  		catch (\Exception $e) {
            $errors[]=$e->getMessage();
            $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
        }
        $vproductModel = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts');
        $vproductModel->addData(isset($productData['product'])?$productData['product']:'');
        $vproductModel->addData(isset($productData['product']['stock_data'])?$productData['product']['stock_data']:'');
        $productErrors=$vproductModel->validate();
    
        if (is_array($productErrors)) {
            $errors = array_merge($errors, $productErrors);
        }
    
        if (!empty($errors)) {
            foreach ($errors as $message) {
                $this->messageManager->addError($message);
            }
            return \Ced\CsMarketplace\Model\Vproducts::ERROR_IN_PRODUCT_SAVE;
        }
        return $product;
    
    }
    
    /**
     * Initialize product saving
     *
     * @return Magento\Catalog\Model\Product|const ERROR_IN_PRODUCT_SAVE
     */
    protected function _initProductSave()
    {
    
        $product     = $this->_initProduct();
        if($product== \Ced\CsMarketplace\Model\Vproducts::ERROR_IN_PRODUCT_SAVE) {
            return \Ced\CsMarketplace\Model\Vproducts::ERROR_IN_PRODUCT_SAVE; 
        }
        $productData = $this->getRequest()->getPost('product');
        $productId  = (int) $this->getRequest()->getParam('id');
    
        if ($productData) {
            $stock_data = isset($productData['stock_data'])?$productData['stock_data']:'';
            $this->_filterStockData($stock_data);
        }
    
        $product->addData($productData);
        /**
         * Initialize product categories
        */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = '';
            }
            $cats = explode(',', $categoryIds);
            $cats=array_unique($cats);
            $category_array = array ();
            foreach ( $cats as $value ) {
                if (strlen($value)) {
                    $category_array [] = trim($value);
                }
            }
            $product->setCategoryIds($category_array);
        }
    
        if ($this->mode==\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE) {
            $setId = (int) $this->getRequest()->getParam('set')? (int) $this->getRequest()->getParam('set'):$this->_objectManager->get('Magento\Catalog\Model\Product')->getDefaultAttributeSetId();;
            $product->setAttributeSetId($setId);
    
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            $product->setStatus($this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->isProductApprovalRequired()?\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED:\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            if ($this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isSharingEnabled()) {
                $websiteIds = isset($productData['website_ids']) ? $productData['website_ids'] : [];
            } else {
                $websiteIds = array($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website')); 
            }
            $product->setWebsiteIds($websiteIds);   
        }

        if ($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode()) {
            $product->setWebsiteIds(array($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(true)->getWebsite()->getId()));
        }
        return $product;
    }
    
    /**
     * Filter product stock data
     *
     * @param  array $stockData
     * @return null
     */
    protected function _filterStockData(&$stockData)
    {
        if (is_null($stockData)) {
            return false;
        }
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 1;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }

        return $this;
    }
}
