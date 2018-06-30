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
class Vendor extends \Ced\CsMarketplace\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ced_csmarketplace_vendor';
    
    protected $_dataSaveAllowed = true;
    protected $_cacheTag = true;
    
    protected $_customer = false;
    const VENDOR_NEW_STATUS = 'new';
    const VENDOR_APPROVED_STATUS = 'approved';
    const VENDOR_DISAPPROVED_STATUS = 'disapproved';
    const VENDOR_DELETED_STATUS = 'deleted';
    const VENDOR_SHOP_URL_SUFFIX = '.html';
    const DEFAULT_SORT_BY = 'name';
    
    const XML_PATH_VENDOR_WEBSITE_SHARE = "ced_csmarketplace/vendor/customer_share";
    public $_vendorstatus = null;

    
    /**
     * @param \Magento\Framework\Model\Context                  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory                             $customAttributeFactory
     * @param ResourceModel\Vendor                              $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb     $resourceCollection
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Ced\CsMarketplace\Helper\Data $dataHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        ResourceModel\Vendor $resource = null,
        ResourceModel\Vendor\Collection $resourceCollection = null,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Catalog\Model\Product\Url $url,
        array $data = []
    ) {

        $this->_dataHelper = $dataHelper;
        $this->_aclHelper = $aclHelper;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $objectInterface,
            $url,
            $data
        );
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Vendor');
    }


    /**
     * Load vendor by customer id
     * @param $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomerId($customerId) 
    {
        return $this->loadByAttribute('customer_id', $customerId);
    }
    

    /**
     * Load vendor by vendor/customer email
     * @param $email
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByEmail($email) 
    {
        return $this->loadByAttribute('email', $email);
    }
    
    /**
     * Set customer
     */
    public function setCustomer($customer) 
    {
        $this->_customer = $customer;
        return $this;
    }
    
    /**
     * Get customer
     */
    public function getCustomer() 
    {
        if(!$this->_customer && $this->getCustomerId()) {
            $this->_customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($this->getCustomerId());
        }
        return $this->_customer;
    }
    
    /**
     * Check vendor is active|approved
     *
     * @return bool
     */
    public function getIsActive() 
    { 
        if($this->getData('status') == self::VENDOR_APPROVED_STATUS) { return true; 
        }
        return false;
    }


    /**
     * Get UrlSuffix
     */
    public function getUrlSuffix() {
        $suffix = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vseo/general/marketplace_url_suffix');
        return $suffix ? $suffix : self::VENDOR_SHOP_URL_SUFFIX ;
    }
    
    /**
     * Get Urlpath for vendor shop
     */
    public function getUrlPath() {
        if($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Ced_CsSeoSuite') && $this->_objectManager->get('Ced\CsSeoSuite\Helper\Data')->isEnabled()){
            return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vseo/general/marketplace_url_key');
        }
        return 'vendor_shop';
    }
    
    /**
     * get vendor shop url key
     *
     * @param  string $shop_url
     * @return string
     */
    public function getShopUrlKey($shop_url = '') 
    {
                
         if (strlen($shop_url)) {
            return str_replace($this->getUrlSuffix(), '', trim($shop_url));
        } elseif ($this->getId()) {
            return str_replace($this->getUrlSuffix(), '', trim($this->getShopUrl()));
        } else {
            return $shop_url;
        }
    }
    
    /**
     * get vendor shop url
     *
     * @return string
     */
    public function getVendorShopUrl() 
    {
        $urlpath = '';
        $urlpath = $this->getUrlPath();
        if(strlen($urlpath)>0) {
            $url = $urlpath.'/'.trim($this->getShopUrl()).$this->getUrlSuffix();
        } else {
            $url = trim($this->getShopUrl()).$this->getUrlSuffix();
        }
        $url = $this->_objectManager->get('Ced\CsMarketplace\Model\Url')->getShopUrl($url);
        return rtrim(trim("{$url}"), '/');
    }

    /**
     * Register a vendor
     */
    public function register($vendorData = []) 
    {
        $customer = $this->getCustomer();

        if($customer && isset($vendorData['public_name']) && isset($vendorData['shop_url'])) {
        
            if ($vendorData && count($vendorData)) {
                $vendorData = array_merge($vendorData, $this->_aclHelper->getDefultAclValues()); 
            } else {
                $vendorData = $this->_aclHelper->getDefultAclValues(); 
            }
            $vendorData['name']        = $customer->getFirstname().' '.$customer->getLastname();
            $vendorData['gender']      = $customer->getGender();
            $vendorData['email']       = $customer->getEmail();                
            $vendorData['customer_id'] = $customer->getId();
            $vendorData['created_at'] = date('Y-m-d H:i:s');
            $this->addData($vendorData);
                
            if ($this->validate(array_keys($vendorData))) {
                $this->setErrors('');
                return $this;
            } else {
                return $this;
            }
        }
        return false;        
    }
    
    /**
     * Processing object before save data
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    public function beforeSave()
    {
        $attributes = $this->getVendorAttributes()->addFieldToFilter('frontend_input',array('image','file'));	
        $images = $this->_objectManager->get('Ced\CsMarketplace\Helper\Image')->UploadImage($attributes);
        
        $this->addData($images);
        $customer = $this->getCustomer();
        if($customer) {
            $this->addData(array('website_id'=>$customer->getWebsiteId()));
        }
        
        if(!$this->getMassFlag()) {
            
            $previousStatus = $this->getOrigData('status');
            if(!$previousStatus) {
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->sendAccountEmail($this->getStatus(), '', $this);
            }
            if($previousStatus!= '' &&$this->getStatus() != $previousStatus) {
                $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                    ->changeProductsStatus(array($this->getId()), $this->getStatus());
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->sendAccountEmail($this->getStatus(), '', $this);
            }
        }

        if ($this->getData('shop_url')) {
            $shopUrlKey = $this->formatShopUrl($this->getData('shop_url'));
            if(!$this->getId() || ($this->getId() && $this->getData('shop_url') != $this->getOrigData('shop_url'))) {
                
                $shopUrlKey = $this->genrateShopUrl($this->getData());
                
                if(strlen($this->getUrlPath()) == 0 && $this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Ced_CsSeoSuite') && $this->_objectManager->get('Ced\CsSeoSuite\Helper\Data')->isEnabled()) {
                    $urlExist = $this->_objectManager->create('Ced\CsSeoSuite\Model\Url')
                                ->getCollection()
                                ->addFieldToFilter('request_path',array('eq'=>$this->getOrigData('shop_url').$this->getUrlSuffix()))
                                ->addFieldToFilter('target_path',array('eq'=>'csmarketplace/vshops/view/shop_url/'.$this->getOrigData('shop_url')));
                                       
                    $params = array();
                    if(count($urlExist) > 0) {
                        $urlExist = $urlExist->getLastItem();
                        if($urlExist && $urlExist->getId()) {
                            $params['id'] = $urlExist->getId();
                            $params['is_edit'] = 1;
                        }
                    }
                    $params['name'] = 'Marketplace Shop Page - '.$shopUrlKey;
                    $params['request_path'] = $shopUrlKey.$this->getUrlSuffix();
                    $params['target_path'] = 'csmarketplace/vshops/view/shop_url/'.$shopUrlKey;
                    $params['description'] = '';
                    $this->saveRewrite($params);
                    
                }
            }
            $this->setData('shop_url',$shopUrlKey); 
        }
        parent::beforeSave();
        return $this;
    }
    
    /**
     * Genrate the vendor shop url
     *
     */
    public function genrateShopUrl($data = array() ,$result = array('success'=>0), $count = 0) {
        if (isset($result['success']) && !$result['success']) {
            $shopUrlKey = $this->getUnusedPath($data['shop_url'], $count, $this->getUrlSuffix());
            $tempUrlKey = $data['shop_url'];
            $data['shop_url'] = $shopUrlKey;
            $result = $this->checkAvailability($data);
            if (isset($result['success']) && $result['success']) {
                return $shopUrlKey;
            } else {
                $data['shop_url'] = $tempUrlKey;
                return $this->genrateShopUrl($data,$result,$count+1);
            }
        } else{
            return false;
        }
    }

    /**
     * Urlrewrite save action
     *
     */
    public function saveRewrite($data)
    {
        if ($data) {
            if($this->_registry->registry('current_urlrewrite'))
                $this->_registry->unregister('current_urlrewrite');

            if($this->_registry->registry('current_urlrewrite_collection'))
                $this->_registry->unregister('current_urlrewrite_collection');

            $id = isset($data['id']) ? $data['id'] : 0;
            $this->_registry->register('current_urlrewrite', $this->_objectManager->create('Ced\CsSeoSuite\Model\Url')->load($id));
            if($this->_registry->registry('current_urlrewrite')->getId()) {
                $urlRewrite = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite')
                               ->getCollection()
                               ->addFieldToFilter('url_rewrite_id',array('in'=>$this->_registry->registry('current_urlrewrite')->getUrlRewriteIds()));
                $this->_registry->register('current_urlrewrite_collection',$urlRewrite);
            }

            $isEdit = isset($data['is_edit']) ? $data['is_edit'] : 0;
            try {
                $requestPath = $data['request_path'];
                $this->_objectManager->get('Magento\UrlRewrite\Helper\UrlRewrite')->validateRequestPath($requestPath);

                $urlRewriteIds = [];
                if($this->_registry->registry('current_urlrewrite_collection') && count($this->_registry->registry('current_urlrewrite_collection')) > 0) {
                    foreach($this->_registry->registry('current_urlrewrite_collection') as $model) {
                        $model->setRequestPath($requestPath);
                        if ($isEdit){
                            $model->setTargetPath($data['target_path']);
                        }
                        $model->save();
                        $urlRewriteIds[] = $model->getUrlRewriteId();
                    }
                } else {
                    $stores = $this->_objectManager->create('Magento\Store\Model\Store');
                    $stores = $stores->getCollection();
                    foreach ($stores as $store) {
                        if ($store->getId()) {
                            $model = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
                            $model->setIsAutogenerated(0)
                                   ->setStoreId($store->getId())
                                   ->setTargetPath($data['target_path'])
                                   ->setDescription($data['description'])
                                   ->setRequestPath($requestPath)
                                   ->save();
                            $urlRewriteIds[] = $model->getUrlRewriteId();
                        }
                    }
                }
                $seoUrlModel = $this->_registry->registry('current_urlrewrite');
                if ($isEdit) {
                    $seoUrlModel->setName($data['name']);
                    $seoUrlModel->setRequestPath($requestPath);
                    $seoUrlModel->setTargetPath($data['target_path']);
                }
                $seoUrlModel->setUrlRewriteIds($urlRewriteIds);
                if (!$seoUrlModel->getId()) {
                    $seoUrlModel->setIsAutogenerated(0)
                                ->setName($data['name'])
                                ->setStoreId(0)
                                ->setTargetPath($data['target_path'])
                                ->setDescription($data['description']);
                }
                $seoUrlModel->setRequestPath($requestPath)->save();
                $model->save();               
                return true;
            }
            catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger\Monolog')->critical($e->getMessage());
               return false;
            }
        } else{
            return false;
        }
    }
    

    /**
     * Check for empty values for provided Attribute Code on each entity
     * @param array $entityIds
     * @param array $values
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMassAttribute(array $entityIds, array $values)
    {
        if ($values['code'] == "status") {
            if(!isset($values['code']) || !isset($values['value'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('New values was missing.'));
            }
            if ($this->_massCollection == null) {
                $collection = $this->getResourceCollection()->addAttributeToSelect($values['code'])
                                ->addAttributeToFilter('entity_id', array('in'=>$entityIds));
            } else {
                $collection = $this->_massCollection;
            }
            if (count($collection)) {
                $vendorIds = [];
                $this->_massCollection = $collection;
                
                foreach ($collection as $model) {
                    $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($model->getId());
                    $vendorstatus = $vendor->getStatus();
                    $vendor->setData($values['code'], $values['value'])->setMassFlag(true);
                    if (!$vendor->validate(array($values['code']))) {
                        if($vendor->getErrors()) {
                            foreach ($vendor->getErrors() as $error) {
                                $this->messageManager->addError($error); 
                            }
                        }
                        continue;
                    }
                    $vendor->save();                
                    if ($vendorstatus != '' && $vendor->getStatus() != $vendorstatus) {
                        $vendorIds[] = $vendor->getId();

                        /**
                          * dispatch event when vendor's account status is changed
                          */
                        $this->_objectManager->get('\Magento\Framework\Event\ManagerInterface')
                            ->dispatch('csmarketplace_vendor_status_changed', ['vendor' => $model ]);
                        $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->sendAccountEmail($vendor->getStatus(), '', $vendor);
                    }
                }
                if (count($vendorIds)>0) {
                    $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->changeProductsStatus($vendorIds, $values['value']); 
                }
                return true;
            }
            return null;
        }
        else {
            parent::saveMassAttribute($entityIds, $values); 
        }
        return false;
    }
    
    public function delete() 
    {
        $this->_eventManager->dispatch($this->_eventPrefix.'_delete_before', array('vendor' => $this));
        parent::delete();
        $this->_eventManager->dispatch($this->_eventPrefix.'_delete_after', array('vendor' => $this));
    }
    

    /**
     * Return Entity Type instance
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityType() 
    {
        return $this->_getResource()->getEntityType();
    }


    /**
     * Return Entity Type ID
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityTypeId() 
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }
    

    /**
     * Retrieve vendor attributes
     * if $groupId is null - retrieve all vendor attributes
     * @param null $groupId
     * @param bool $skipSuper
     * @param int $storeId
     * @param null $visibility
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributes($groupId = null, $skipSuper = false, $storeId = 0, $visibility = null)
    {
        $typeId = $this->getEntityTypeId();
        if ($groupId) {
            $vendorAttributes = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')->getCollection()
                                    ->setAttributeGroupFilter($groupId)->load();
            if ($storeId) { 
                $vendorAttributes->setStoreId($storeId); 
            }
            if ($visibility != null) { 
                $vendorAttributes->addFieldToFilter('is_visible', array('gt'=>$visibility)); 
            }
            $this->_eventManager->dispatch('ced_csmarketplace_vendor_group_wise_attributes_load_after', array('groupId'=>$groupId,'vendorattributes'=>$vendorAttributes));
            $attributes = [];
            foreach ($vendorAttributes as $attribute) {
                if ($attribute->getData('entity_type_id') == $typeId && $attribute->getData('attribute_code') != 'website_id') {
                    $attributes[] = $attribute;
                }
            }
        } else {
            $attributes = $vendorAttributes;
        }
        return $attributes;
    }

    /**
     * Retrieve All vendor Attributes
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getVendorAttributes() 
    {
        return $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')
                    ->setEntityTypeId($this->getEntityTypeId())
                    ->setStoreId(0)
                    ->getCollection()
                    ->addFieldToFilter('entity_type_id', $this->getEntityTypeId());
    }

    /**
     * Retrieve Frontend vendor Attributes
     * @param int $editable
     * @param string $sort
     * @return mixed
     */
    public function getFrontendVendorAttributes($editable = 0, $sort = 'ASC') 
    {
        return $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor\Attribute')
                    ->setStoreId($this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())
                    ->getCollection()
                    ->addFieldToFilter('is_visible', array('eq'=>$editable))
                    ->setOrder('sort_order', $sort);
    }

    /**
     * Retrieve vendor Orders
     * @param int $vendorId
     * @return mixed
     */
    public function getAssociatedOrders($vendorId = 0) 
    {
        if (!$vendorId && $this->getId()) { 
            $vendorId = $this->getId(); 
        }
        $orderGridTable = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('sales_order_grid');

        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getCollection()
                        ->addFieldToFilter('vendor_id', $vendorId);
        $collection->getSelect()->join($orderGridTable, 'main_table.order_id LIKE  CONCAT('.$orderGridTable.".increment_id".')', array('billing_name','increment_id','status','store_id','store_name','customer_id','base_grand_total','base_total_paid','grand_total','total_paid','base_currency_code','order_currency_code','shipping_name','billing_address','shipping_address','shipping_information','customer_email','customer_group','subtotal','shipping_and_handling','customer_name','payment_method','total_refunded'));

        return $collection;
    }

    /**
     * @param array $groups
     * @param int $vendor_id
     */
    public function savePaymentMethods($groups = [], $vendor_id = 0) 
    {
        if (!$vendor_id && $this->getId()) { 
            $vendor_id = $this->getId(); 
        }
        $section = \Ced\CsMarketplace\Model\Vsettings::PAYMENT_SECTION;
        if(strlen($section) > 0 && $vendor_id && count($groups)>0) {
            foreach ($groups as $code=>$values) {
                foreach ($values as $name=>$value) {
                    $serialized = 0;
                    $key = strtolower($section.'/'.$code.'/'.$name);
                    if (is_array($value)) {  
                        $value = serialize($value); 
                        $serialized = 1; 
                    }
                    
                    $setting = $this->_objectManager->get('Ced\CsMarketplace\Model\Vsettings')
                                ->loadByField(array('`key`','`vendor_id`'), array($key,$vendor_id));
                    if ($setting && $setting->getId()) {
                        $setting->setVendorId($vendor_id)
                            ->setGroup($section)
                            ->setKey($key)
                            ->setValue($value)
                            ->setSerialized($serialized)
                            ->save();
                    } else {
                        $setting = $this->_objectManager->get('Ced\CsMarketplace\Model\Vsettings');
                        $setting->setVendorId($vendor_id)
                            ->setGroup($section)
                            ->setKey($key)
                            ->setValue($value)
                            ->setSerialized($serialized)
                            ->save();
                    }
                }
            }
        }
    }
    
    /**
     * Retrieve vendor Payment Methods
     *
     * @param  int $vendorId
     * @return Array $methods
     */
    public function getPaymentMethods($vendorId = 0) 
    {
        if (!$vendorId && $this->getId()) { 
            $vendorId = $this->getId(); 
        }
        $availableMethods = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Paymentmethods')->toOptionArray();
        $methods = [];
        if (count($availableMethods)>0) {
            foreach($availableMethods as $method) {
                if (isset($method['value'])) {
                    $object = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Payment\Methods\\'.ucfirst($method['value']));
                    if (is_object($object)) {
                        $methods[$method['value']] = $object;
                    }
                }
            }
        }
        return $methods;
    }
    
    /**
     * Retrieve vendor Payment Methods
     *
     * @param  int $vendorId
     * @return Array $methods
     */
    public function getPaymentMethodsArray($vendorId = 0, $all = true) 
    {
        if (!$vendorId && $this->getId()) { 
            $vendorId = $this->getId(); 
        }
        $methods = $this->getPaymentMethods($vendorId);
        $options = [];
        if ($all) { 
            $options[''] = ''; 
        }
        if (count($methods) > 0) {
            foreach($methods as $code=>$method) {
                $key = strtolower(\Ced\CsMarketplace\Model\Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/active');
                $setting = $this->_objectManager->get('Ced\CsMarketplace\Model\Vsettings')
                            ->loadByField(array('key','vendor_id'), array($key,(int)$vendorId));
                if ($setting && $setting->getId() &&  $setting->getValue()) {
                    $options[$code] = $method->getLabel('label');
                }
            }
        }
        if ($all) { 
            $options['other'] = __('Other'); 
        }
        return $options;
    }
    
    public function getAssociatedPayments($vendorId = 0) 
    {
        if (!$vendorId) { 
            $vendorId = $this->getId(); 
        }
        return $this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment')->getCollection()
                    ->addFieldToFilter('vendor_id', array('eq'=>$vendorId))
                    ->setOrder('created_at', 'DESC');
    }
    
    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate($attribute = null)
    {
        
        $errors = array();
        if ($attribute != null) {
            if (!is_array($attribute)) { $attribute = array($attribute); 
            } 
        }
        
        $attributes = $this->getVendorAttributes();
        if(is_array($attribute) && count($attribute) > 0) {
            $attributes->addFieldToFilter('attribute_code', array('in'=>$attribute));
        }
        $tmp = array();
        foreach($attributes as $attribute) {
             
             if($attribute->getFrontendInput()=='image' || $attribute->getFrontendInput()=='file'){
        		try{
        			if($this->getData($attribute->getAttributeCode())=='' && $attribute->getIsRequired())
        				$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => "vendor[{$attribute->getAttributeCode()}]"));
        		}
        		catch(\Exception $e){
        			$errors[] = $attribute->getFrontend()->getLabel()." is a required Field";
        		}
        		continue;
        	}
            $tmp[] = array('Attribute Label'=>$attribute->getFrontend()->getLabel(),'Attribute Code'=>$attribute->getAttributeCode(),'Value'=>$this->getData($attribute->getAttributeCode()));
            $terrors = $this->zendValidate($attribute->getFrontend()->getLabel(), $this->getData($attribute->getAttributeCode()), $attribute->getFrontend()->getClass(), $attribute->getIsRequired());
            foreach($terrors as $terror) {
                $errors[] = $terror;
            }
        }
    
        if (count($errors) == 0) {
            return true;
        } else {
            $this->setErrors($errors);
        }
        
        return false;
    }
    
    /**
     * Extract non editable vendor attribute data
     */
    public function extractNonEditableData() 
    {
        if ($this->getId()) {
            
            $nonEditableAttributes = $this->getFrontendVendorAttributes(0, 'ASC');
            foreach ($nonEditableAttributes as $attribute) {
                $this->setData($attribute->getAttributeCode(), $this->getOrigData($attribute->getAttributeCode()));
            }
            foreach (array('shop_url','status','group','created_at', 'email','shop_disable') as $attribute_code) {
                $this->setData($attribute_code, $this->getOrigData($attribute_code));
            }
        }
        return $this;
    }
    
    /**
     * Retrieve vendor Payments
     *
     * @param  int $vendorId Retrieve payments
     * @return Ced_CsMarketplace_Model_Resource_Vpayment_Collection
     */
    public function getVendorPayments($vendorId = 0) 
    {
        
        if(!$vendorId) { $vendorId = $this->getId(); 
        }
        
        $collection =  $this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment')->getCollection()->addFieldToFilter('vendor_id', array('eq'=>$vendorId));
        return $collection;
    }
    
    public function getDefaultSortBy() 
    {
        return self::DEFAULT_SORT_BY;
    }
    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
            self::DEFAULT_SORT_BY  => __('Name')
        );
        // foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            // /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            // $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        // }

        return $options;
    }
    
    
    /**
     *Retrieve Website Ids
     *
     *@param  $vendor
     * @return array $websiteIds
     */
    public function getWebsiteIds($vendor=null)
    {
        if(!$vendor && $this->getId()) { 
            $vendor = $this; 
        }
        if (is_numeric($vendor)) {
            $vendor = $this->load($vendor);
        } 
        if ($vendor && $vendor->getId()) {
            
            if($this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isSharingEnabled()) {
                return array_keys($this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->getCollection()->toOptionHash());
            }
            else {              
                return array($vendor->getWebsiteId()); 
            }
        }
        return array();
    }
    
    public function deleteFromGroup()
    {
        $this->_getResource()->deleteFromGroup($this);
        return $this;
    }
    
    public function groupVendorExists()
    {
        $result = $this->_getResource()->groupVendorExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }
    
    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    public function vendorExists()
    {
        $result = $this->_getResource()->vendorExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }
    
    /**
     * Get vendor ACL group
     *
     * @return string
     */
    public function getAclGroup()
    {
        return 'U' . $this->getId();
    }
    /**
     * Retrieve configuration for all attributes
     *
     * @param null|\Magento\Framework\DataObject $object
     * @return $this
     */
   /*  public function loadAllAttributes($object = null)
    {
    	 
    	return $this->getAttributeLoader()->loadAllAttributes($this, $object);
    } */

}
