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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
/**
 * Vendor Product model
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author     CedCommerce Core Team <connect@cedcommerce.com>
 */

namespace Ced\CsProduct\Model;
use Magento\Framework\Api\AttributeValueFactory;

class Vproducts extends \Ced\CsMarketplace\Model\Vproducts {

	protected $_vproducts=array();

    /**
     * Vproducts constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
        array $data = []
    ) {
       parent::__construct($storeManager, $context, $objectInterface, $registry, $extensionFactory, $customAttributeFactory);
    }
	/**
	 * Check Product Admin Approval required
	 */
	 public function isProductApprovalRequired(){
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');		
		return $scopeConfig->getValue('ced_vproducts/general/confirmation','store',$storeManager->getStore()->getId());
	}

	public function saveProduct($mode) {
		
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
	   if(!$scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			return parent::saveProduct($mode);
			
		}
		
		$register = $this->_objectManager->get('\Magento\Framework\Registry');
		if($scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation','store',$storeManager->getStore()->getId())){
			$product=array();
			if($register->registry('saved_product')!=null)
				$product = $register->registry('saved_product');

			$productData = array();
			//$productData = $this->getRequest()->getPostValue();
			/**
			 * Relate Product data
			 * @params int mode,int $productId,array $productData
			 */
			$vproductModel = $this->processPostSave($mode,$product,$productData);

		}
		return $this;
	}
	/**
	 * Relate Product Data
	 * @params $mode,int $productId,array $productData
	 */

	public function processPostSave($mode,$product,$productData){
		
		
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		if(!$scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			
			return parent::processPostSave($mode,$product,$productData);
			
		}
		
		$stockRegistry = $this->_objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface');
		$stockitem = $stockRegistry->getStockItem(
									$product->getId(),
									$product->getStore()->getWebsiteId()
								);
		$qty = $stockitem->getQty();
		$is_in_stock = $stockitem->getIsInStock();
		
		$productId = $product->getId();
		 $websiteIds='';
        if(isset($productData['product']['website_ids'])) {
            $websiteIds=implode(",", $productData['product']['website_ids']); 
        }
        else if($this->_registry->registry('ced_csmarketplace_current_website')!='') {
            $websiteIds=$this->_registry->registry('ced_csmarketplace_current_website'); 
        }
        else { 
            $websiteIds=implode(",", $product->getWebsiteIds()); 
        }
		$storeId = $storeManager->getStore()->getId();
		
		$vendorId = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
		
		switch($mode) {
			case self::NEW_PRODUCT_MODE:
										$vproductsModel = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts');
										$vproductsModel->setVendorId($vendorId);
										$vproductsModel->setProductId($productId);
										$vproductsModel->setData('type',$product->getTypeId());
										$vproductsModel->setPrice($product->getPrice());
										$vproductsModel->setSpecialPrice($product->getSpecialPrice());
										$vproductsModel->setName($product->getName());
										$vproductsModel->setDescription($product->getDescription());
										//$vproductsModel->setShortDescription($product->getShortDescription());
										$vproductsModel->setSku($product->getSku());
										if(isset($productData['default_approval'])){
										    $vproductsModel->setCheckStatus(self::APPROVED_STATUS);
										}else{
										    $vproductsModel->setCheckStatus($this->isProductApprovalRequired()?self::PENDING_STATUS:self::APPROVED_STATUS);
										}
										$vproductsModel->setQty($qty);
										$vproductsModel->setIsInStock($is_in_stock);
										$vproductsModel->setWebsiteId($websiteIds);
										
										return $vproductsModel->save();
										
			case self::EDIT_PRODUCT_MODE:

									$model=$this->loadByField(array('product_id'),array($product->getId()));
									
									if($model && $model->getId()){
										
										$model->setData('type',$product->getTypeId());
										$model->setPrice($product->getPrice());
										$model->setSpecialPrice($product->getSpecialPrice());
										$model->setName($product->getName());
										$model->setDescription($product->getDescription());
										$model->setShortDescription($product->getShortDescription());
										$model->setSku($product->getSku());
										//$model->setCheckStatus($model->getCheckStatus());
										$model->setQty($qty);
										$model->setIsInStock($is_in_stock);
										$model->setWebsiteId($websiteIds);
										return $model->save ();									
									}
		}
		return $this;
	}

	/**
	 * get Allowed WebsiteIds
	 *
	 * @return array websiteIds
	 */
	public function getAllowedWebsiteIds() {
			$webisteIds=$this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')
											 ->getWebsiteIds($this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId());
			return $webisteIds;
	}
	
	
}

