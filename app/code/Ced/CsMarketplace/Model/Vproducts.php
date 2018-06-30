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

namespace Ced\CsMarketplace\Model;
use Magento\Framework\Api\AttributeValueFactory;

class Vproducts extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
    
    const NOT_APPROVED_STATUS = 0;
    const APPROVED_STATUS = 1;
    const PENDING_STATUS = 2;    
    const DELETED_STATUS = 3;
    
    const ERROR_IN_PRODUCT_SAVE = "error";
    
    const NEW_PRODUCT_MODE = 'new';
    const EDIT_PRODUCT_MODE = 'edit';
    const AREA_FRONTEND = "frontend";
    
    protected $_vproducts = [];
    protected $_objectManager;
    protected $_registry = null;
    protected $_customerSession = null;
    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory       $extensionFactory
     * @param AttributeValueFactory                                   $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
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
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectInterface;
        $this->_registry = $this->_objectManager->get('Magento\Framework\Registry');
        $this->_customerSession = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession();
        
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }
    /**
     * Initialize vproducts model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Vproducts');
    }
    
    /**
     * Check Product Admin Approval required
     */
    public function isProductApprovalRequired()
    {
        $storeManager=$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $scopeConfig=$this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        
        return $scopeConfig->getValue('ced_vproducts/general/confirmation', 'store', $storeManager->getStore()->getCode());
    }
    
    /**
     * Filter options
     */
    public function getOptionArray() 
    {
        return array (
        self::APPROVED_STATUS => __('Approved'),
        self::PENDING_STATUS=> __('Pending'),
        self::NOT_APPROVED_STATUS => __('Disapproved') 
        );
    }
    
    /**
     * Filter options
     */
    public function getVendorOptionArray() 
    {
        return array (
            self::APPROVED_STATUS.\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED => __('Approved (Enabled)'),
            self::APPROVED_STATUS.\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED => __('Approved (Disabled)'),
            self::PENDING_STATUS=> __('Pending'),
            self::NOT_APPROVED_STATUS => __('Disapproved')
        );
    }
    
    /**
     * Mass action options
     */
    public function getMassActionArray() 
    {
        return [
        self::APPROVED_STATUS  => __('Approved'),
        self::NOT_APPROVED_STATUS => __('Disapproved') 
        ];
    }
    
        

    /**
     * Get Vendor Id by Product|Product Id
     * @param $product
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVendorIdByProduct($product) 
    {
        $vproduct = false;
        if (is_numeric($product)) {
            $vproduct = $this->loadByField('product_id', $product);
        } elseif ($product && $product->getId()) {
            $vproduct = $this->loadByField('product_id', $product->getId());
        }
        if ($vproduct && $vproduct->getId()) {
            return $vproduct->getVendorId();
        }
        return false;
    }



    /**
     * Validate csmarketplace product attribute values.
     * @return array|bool
     * @throws \Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = [];
        
        if (!\Zend_Validate::is(trim($this->getName()), 'NotEmpty')) {
            $errors[] = __('The Product Name cannot be empty');
        }
        if (!\Zend_Validate::is(trim($this->getSku()), 'NotEmpty')) {
            $errors[] = __('The Product SKU cannot be empty');
        }
        
        if($this->getType()==\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
            $weight=    trim($this->getWeight());
            if (!\Zend_Validate::is($weight, 'NotEmpty')) {
                $errors[] = __('The Product Weight cannot be empty');
            }
            else if(!is_numeric($weight)&&!($weight>0)) {
                $errors[] = __('The Product Weight must be 0 or Greater'); 
            }
        }
        
        $qty = trim($this->getQty());
        if (!\Zend_Validate::is($qty, 'NotEmpty')) {
            $errors[] = __('The Product Stock cannot be empty');
        }
        else if(!is_numeric($qty)) {
            $errors[] = __('The Product Stock must be a valid Number'); 
        }
            
        if (!\Zend_Validate::is(trim($this->getTaxClassId()), 'NotEmpty')) {
            $errors[] = __('The Product Tax Class cannot be empty');
        }
        
        $price = trim($this->getPrice());
        if (!\Zend_Validate::is($price, 'NotEmpty')) {
            $errors[] = __('The Product Price cannot be empty');
        }
        else if(!is_numeric($price)&&!($price>0)) {
            $errors[] = __('The Product Price must be 0 or Greater'); 
        }
        
        $special_price = trim($this->getSpecialPrice());
        if($special_price != '') {
            if(!is_numeric($special_price)&&!($special_price>0)) {
                $errors[] = __('The Product Special Price must be 0 or Greater'); 
            }
        }
        
        $shortDescription = strip_tags(trim($this->getShortDescription()));
        $description = strip_tags(trim($this->getDescription()));    
        if (strlen($shortDescription) == 0) {
            $errors[] = __('The Product Short description cannot be empty');
        }
        if (strlen($description) == 0) {
            $errors[] = __('The Product Description cannot be empty');
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
    
    /**
     * Save Product
     *
     * @params $mode
     * @return int product id
     */
    public function saveProduct($mode) 
    {
        $product = $this->getProductData();    
        $productData = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParams();
        $productId = $product->getId();
        
        /**
         * Save Stock data
      *
         * @params int $productId,array $stockdata
         */
         
        
        $product = $this->saveStockData($product, $product->getStockData());
        
        
        /**
         * Relate Product data
      *
         * @params int mode,int $productId,array $productData
         */
        $this->processPostSave($mode, $product, $productData);
        
        /**
         * Save Product Type Specific data
      *
         * @params int $productId,array $productData
         */
        $this->saveTypeData($productId, $productData);
        
        /**
         * Save Product Images
      *
         * @params int $productId,array $productData
         */
         
        $this->_objectManager->get('Ced\CsMarketplace\Helper\Vproducts\Image')->saveImages($product, $productData);
        
        
        /**
         * Send Product Mails
      *
         * @params array productid,int $status
         */
        if(!$this->isProductApprovalRequired() && $mode == self::NEW_PRODUCT_MODE) {
            $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')
                ->sendProductNotificationEmail(array($productId), \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS);
        }
       return $this;
    }
    
    /**
     * Save Product Stock data
     *
     * @params int $productId,array $productData
     * @return int product id
     */
    private function saveStockData($product,$stockData)
    {        
        
        if ($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Magento_CatalogInventory')) {
        
            if(!is_array($stockData)) {
                $stockData=array();
            }
            $stockData['is_in_stock'] = isset($stockData['is_in_stock']) ? $stockData['is_in_stock'] : 1;
            
            $stockData['qty'] = isset($stockData['qty']) ? $stockData['qty'] : (int)$this->getQty();
            
            $stockData['is_in_stock'] = isset($stockData['is_in_stock']) ? $stockData['is_in_stock'] : 1;
            
            $stockData['use_config_manage_stock']= isset($stockData['use_config_manage_stock']) ? $stockData['use_config_manage_stock'] : 1;
            
            $stockData['is_decimal_divided'] = isset($stockData['is_decimal_divided']) ? $stockData['is_decimal_divided'] : 0;
            
            $stockItem = $this->_objectManager->get('Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory')->create();
            $this->_objectManager->get('Magento\Framework\Api\DataObjectHelper')->populateWithArray(
                $stockItem,
                $stockData,
                '\Magento\CatalogInventory\Api\Data\StockItemInterface'
            );
            $stockItem->setProduct($product);
            $product->setStockItem($stockItem);
            
        }
        return $product;
    }
    
    /**
     * Save Product Type Specific data
     *
     * @params int $productId,array $productData
     * @return int product id
     */
    private function saveTypeData($productId,$productData)
    {
        $type = isset($productData['type']) ? $productData['type'] : \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE;
        
        switch($type){
        case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:$this->saveDownloadableData($productId, isset($productData['downloadable']) ? $productData['downloadable'] : []);
            break;
            
        default:
            return false;
            
        }

        return $this;
    }
    
    /**
     * Save Downloadable product data
     *
     * @params array $data,int id
     * @return int product id
     */
    public function saveDownloadableData($productid,$downloadableData)    
    {
        $linkhelper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Vproducts\Link');
    
        /**
         * Start uploading data
        */
        $samples = $linkhelper->uploadDownloadableFiles("samples", isset($downloadableData['sample']) ? $downloadableData['sample'] : []);
        $link_samples = $linkhelper->uploadDownloadableFiles("link_samples", isset($downloadableData['link']) ? $downloadableData['link'] : []);
        $links = $linkhelper->uploadDownloadableFiles("links", isset($downloadableData['link']) ? $downloadableData['link'] : []);

        /**
         * Start saving links data
        */
        $linkhelper->processLinksData(isset($downloadableData['link']) ? $downloadableData['link']: [], $links, $link_samples, $productid);
        $linkhelper->processSamplesData(isset($downloadableData['sample']) ? $downloadableData['sample']: [], $samples, $productid);
        return $this;
    }
    
    /**
     * Get Vproduct status
     *
     * @params $storeId
     */
    public function getStatus($storeId)
    {
        $statusModel = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                        ->loadByField(array('product_id', 'store_id'), array($this->getProductId(), $storeId));
        if ($statusModel && $statusModel->getId()) {
            return $statusModel->getStatus(); 
        } else { 
            return 0; 
        }
    }
    
    /**
     * Set Vproduct status
     *
     * @params $mode,int $productId,array $productData
     */
    public function setStatus($status)
    {    
        if ($this->getStoreId()) {
            $statusAttribute = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product')->getAttribute('status');
            
            if ($statusAttribute->isScopeWebsite()) {
                $website = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreId())->getWebsite();
                $stores = $website->getStoreIds();
            } else if ($statusAttribute->isScopeStore()) {
                $stores = array($this->getStoreId());
            } else {
                $stores = array_keys($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStores());
            }
        } else { 
            $stores = array(0);//admin store
        }   
        foreach ($stores as $store) {    
            $statusModel = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts\Status')
                                ->loadByField(array('product_id','store_id'), array($this->getProductId(), $store));
            if ($statusModel && $statusModel->getId()) {
                if($statusModel->getStatus() != $status) {
                    $statusModel->setStatus($status)->save(); 
                }
            } else {
                $statusModel = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts\Status');
                $statusModel->setStatus($status)
                    ->setStoreId($store)
                    ->setProductId($this->getProductId())
                    ->save();
            }
        }
        return $this;
    }
    
    
    /**
     * Relate Product Data
     *
     * @params $mode,int $productId,array $productData
     */
    public function processPostSave($mode, $product, $productData)
    {
        $websiteIds = '';
        if(isset($productData['product']['website_ids'])) {
            $websiteIds = implode(",", $productData['product']['website_ids']); 
        }
        else if($this->_registry->registry('ced_csmarketplace_current_website') != '') {
            $websiteIds = $this->_registry->registry('ced_csmarketplace_current_website'); 
        }
        else { 
            $websiteIds = implode(",", $product->getWebsiteIds()); 
        }
        $productId = $product->getId();
        $storeId = $this->getStoreId();
        switch($mode) {
        case self::NEW_PRODUCT_MODE:
            $prodata = isset($productData['product']) ? $productData['product']: [];
            $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->setData($prodata)
                ->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:0)
                ->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:1)
                ->setPrice($product->getPrice())
                ->setSpecialPrice($product->getSpecialPrice())
                ->setCheckStatus($this->isProductApprovalRequired()?self::PENDING_STATUS:self::APPROVED_STATUS)
                ->setProductId($productId)
                ->setVendorId($this->_customerSession->getVendorId())
                ->setType(isset($productData['type'])?$productData['type']:\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE)
                ->setWebsiteId($websiteIds)
                ->setStatus($this->isProductApprovalRequired()?\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED:\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->save();

        case self::EDIT_PRODUCT_MODE:
            $model = $this->loadByField(array('product_id'), array($product->getId()));
            if($model && $model->getId()) {
                $model->addData(isset($productData['product'])?$productData['product']:[]);
                $model->addData(isset($productData['product']['stock_data'])?$productData['product']['stock_data']:[]);
                $model->addData(array('store_id'=>$storeId,'website_ids'=>$websiteIds,'price'=>$product->getPrice(),'special_price'=>$product->getSpecialPrice()));
                $model->setStatus(isset($productData['product']['status'])?$productData['product']['status']:\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                $this->extractNonEditableData($model);
                $model->save();
            }
        }
    }
    
    /**
     * Change Vproduct status
     *
     * @params int $productId,int checkstatus
     */
     /**
     * Change Vproduct status
     *
     * @params int $productId,int checkstatus
     */
    public function changeVproductStatus($productIds, $checkstatus)
    {
        foreach ($this->_storeManager->getStores() as $store) {
            $storeId[] = $store->getId();
        }
        $storeId = 0;
       
        if (is_array($productIds)) {
            $VproductCollection = $this->getCollection()->addFieldToFilter('product_id', array('in'=>$productIds));
            if (count($VproductCollection)>0) {
                $ids = [];
                $errors = array('success'=>0,'error'=>0);
                foreach ($VproductCollection as $row) {
                    if ($row && $row->getId()) {
                        if(!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->canShow($row->getVendorId()) && $checkstatus!=\Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS) {
                            $errors['error'] = 1;
                            continue;
                        }
                        if($row->getCheckStatus() != $checkstatus) {
                            $productId = $row->getProductId();
                            /**
                             * dispatch event when vendor's product status is changed
                             */
                            $this->_objectManager->get('\Magento\Framework\Event\ManagerInterface')->dispatch('csmarketplace_vendor_product_status_changed', [
                                'product'=>$row, 
                                'status'=>$checkstatus
                            ]);
                         
                            switch ($checkstatus){
                            case \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS:
                                if($row->getCheckStatus() == \Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS) {
                                   // foreach ($storeId as $store_id) {
                                        $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)
                                            ->setStoreId($storeId)
                                            ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                                            ->save();
                                  //  }                                  
                                    $row->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);                
                                }
                                else if($row->getCheckStatus()== \Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS) {
                                    $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                                        ->getCollection()->addFieldtoFilter('product_id', $productId);
                                    foreach ($statusCollection as $statusrow){
                                      // foreach ($storeId as $store_id) {  
                                            $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)
                                                    ->setStoreId($storeId)
                                                    ->setStatus($statusrow->getStatus())
                                                    ->save();
                                      //  }    
                                    }
                                }
                                $errors['success'] = 1;
                                break;
                                    
                            case \Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS:
                                if($row->getCheckStatus()== \Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS) {
                                    $row->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                                } elseif ($row->getCheckStatus() == \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS) {
                                    $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                                        ->getCollection()->addFieldtoFilter('product_id', $productId);
                                    foreach ($statusCollection as $statusrow) {
                                       // foreach ($storeId as $store_id) { 
                                            $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)
                                                ->setStoreId($storeId)
                                                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
                                                ->save();
                                       // }                                            
                                    }
                                }
                                $errors['success'] = 1;
                                break;
                                    
                            case \Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS:    
                                $errors['success'] = 1;
                                break;
                            }
                            $ids[] = $productId;
                            $row->setCheckStatus($checkstatus);
                            $row->save();
                        }
                        else { 
                            $errors['success'] = 1; 
                        }
                    }  
                }
                if($ids && !$this->_customerSession->getVendorId()) {
                    $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')
                        ->sendProductNotificationEmail($ids, $checkstatus);
                }
                return $errors;
            }
            return $this;
        }
        return $this;
    }
    
    /**
     *Change Products Status (Hide/show products from frontend on vendor approve/disapprove)
     *
     *@params array $vendorIds,int $status
     *@return boolean
     */
    public function changeProductsStatus($vendorIds,$status)
    {
        if($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS) {
            return false;
        }
        if(is_array($vendorIds)) {
            foreach ($vendorIds as $vendorId){
                $collection = $this->getVendorProducts('', $vendorId);
                foreach ($collection as $row){
                    $productId = $row->getProductId();
                    if($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS) {
                    
                        $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                            ->getCollection()->addFieldtoFilter('product_id', $productId);
                        foreach ($statusCollection as $statusrow){
                            $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)
                                ->setStoreId($statusrow->getStoreId())
                                ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        }
                    }
                    else if($status == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
                        $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                            ->getCollection()->addFieldtoFilter('product_id', $productId);
                        foreach ($statusCollection as $statusrow){
                            $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)
                                ->setStoreId($statusrow->getStoreId())
                                ->setStatus($statusrow->getStatus());
                        }
                    }
                }
            }
        }
        return $this;
    }
    
    /**
     * Get Product collection
     *
     * @return Ced\CsMarketplace\Model\Resource\Vproducts\Collection
     */
    public function getVendorProducts($checkstatus = '', $vendorId = 0, $productId = 0) 
    {
        $vproducts = $this->getCollection();
        if ($checkstatus === '') {
            $vproducts->addFieldToFilter('check_status', array('neq'=>\Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS));
        } else {
            $vproducts->addFieldToFilter('check_status', array('eq'=>$checkstatus));
        }
        if($vendorId) {
            $vproducts->addFieldToFilter('vendor_id', array('eq'=>$vendorId));
        }
        if($productId) {
            $vproducts->addFieldToFilter('product_id', array('eq'=>$productId));
        }
        
        return $vproducts;
    }
    
    /**
     * Delete Vendor Products
     *
     * @params int $vendor Id
     */
    public function deleteVendorProducts($vendorId)
    {
        if($vendorId) {
            $productids = $this->getVendorProductIds($vendorId);
            if (!empty($productids)) {
                $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                        ->getCollection()->addFieldtoFilter('product_id', ["in" => $productids]);
                foreach ($statusCollection as $statusrow){
                    $statusrow->delete();
                }
                $this->_objectManager->get(\Magento\Catalog\Model\Product\Action::class)
                ->updateAttributes($productids, ['status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED], 0);
            }
        }
    }
    
    /**
     * Authenticate vendor-products association
     *
     * @param  int $vendorId,int $productId
     * @return boolean
     */
    public function isAssociatedProduct($vendorId = 0, $productId = 0) 
    {
        if(!$vendorId || !$productId) { 
            return false; 
        }
        
        $vproducts = $this->getVendorProductIds($vendorId);
        if(in_array($productId, $vproducts)) {
            return true; 
        }
        return false;
    }
    
    
    /**
     * Remove Non Editable Attribute data from set values
     *
     * @param \Ced\CsMarketplace\Model\Vproducts $model
     */
    public function extractNonEditableData($model) 
    {
        foreach (array('vendor_id','product_id','check_status') as $attribute_code) { 
            $model->setData($attribute_code, $model->getOrigData($attribute_code)); 
        }
    }
    
    /**
     * get Allowed WebsiteIds
     *
     * @return array websiteIds
     */
    public function getAllowedWebsiteIds()
    {
        return $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getWebsiteIds($this->_customerSession->getVendorId());
    }
    
    /**
     * get Current vendor Product Ids
     *
     * @return array $productIds
     */
    public function getVendorProductIds($vendorId = 0)
    {
        if (!empty($this->_vproducts)) {
            return $this->_vproducts;
        } else {
            $vendorId = $vendorId ? $vendorId : $this->_customerSession->getVendorId();
            $vcollection = $this->getVendorProducts('', $vendorId, 0);
            $productids = [];
            if(count($vcollection) > 0) {
                foreach($vcollection as $data){
                    array_push($productids, $data->getProductId());
                }
                $this->_vproducts = $productids;
            }
        }
        return $this->_vproducts;
    }
    
    public function getProductCountcategory($vid, $categoryId)
    {
            $collection = $this->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vid);
            $products = [];
            $statusarray = [];
            foreach($collection as $productData){
              array_push($products,$productData->getProductId());
            }
            
            $cedProductcollection = $this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                  ->addAttributeToSelect($this->_objectManager->create('Magento\Catalog\Model\Config')
                  ->getProductAttributes())
                  ->addAttributeToFilter('entity_id',array('in'=>$products))                  
                  ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                  ->addStoreFilter($this->_storeManager->getStore()->getId());
           $cat_id = $categoryId;
            if(isset($cat_id)) {
              $cedProductcollection->joinField(
                  'category_id', 'catalog_category_product', 'category_id',
                  'product_id = entity_id', null, 'left'
              )
              ->addAttributeToSelect('*')
              ->addAttributeToFilter('category_id', array(
                  array('finset', array('in'=>explode(',', $cat_id)))
              ));
            }
             
            return $cedProductcollection->count();
    }
    /**
     * Get products count in category
     *
     * @param  unknown_type $category
     * @return unknown
     */
    public function getProductCount($categoryId)
    {
        $vproducts = $this->getVendorProductIds();
        $resource = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $productTable = $resource->getTableName('catalog_category_product');
        $readConnection = $resource->getConnection('read');
        $select = $readConnection->select();
        $select->from(
            array('main_table'=>$productTable),
            array(new \Zend_Db_Expr('COUNT(main_table.product_id)'))
        )
            ->where('main_table.category_id = ?', $categoryId)
            ->where('main_table.product_id in (?)', $vproducts)
            ->group('main_table.category_id');
        $counts = $readConnection->fetchOne($select);
        return intval($counts);
    }


    public function isApproved($productId = 0)
    {
        if($productId) {
            $model=$this->loadByField(array('product_id'), array($productId));
            if($model && $model->getId()) {
                if($model->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS) {
                    return true; 
                }
                else {
                    return false; 
                }
            }
        }
        return false;
    }
    
}

