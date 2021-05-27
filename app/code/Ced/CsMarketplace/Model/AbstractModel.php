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

namespace Ced\CsMarketplace\Model;

use Magento\Framework\Api\AttributeValueFactory;

abstract class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * Attribute default values
     *
     * This array contain default values for attributes which was redefine
     * value for store
     *
     * @var array
     */
    protected $_defaultValues = array();

    /**
     * This array contains codes of attributes which have value in current store
     *
     * @var array
     */
    protected $_storeValuesFlags = array();

    /**
     * Locked attributes
     *
     * @var array
     */
    protected $_lockedAttributes = array();

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;
    
    /**
     * mass collection
     */
    protected $_massCollection = null;
     
    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $_dataHelper;
    
    /**
     * @var \Ced\CsMarketplace\Helper\Acl
     */
    protected $_aclHelper;
    
    protected $_objectManager;
    
    protected $_url;
    
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
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Catalog\Model\Product\Url $url,
        array $data = []
    ) {
            
        $this->_objectManager = $objectInterface;
        $this->_url = $url;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }
    
    /**
     * Lock attribute
     *
     * @param  string $attributeCode
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function lockAttribute($attributeCode)
    {
        $this->_lockedAttributes[$attributeCode] = true;
        return $this;
    }

    /**
     * Unlock attribute
     *
     * @param  string $attributeCode
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unlockAttribute($attributeCode)
    {
        if ($this->isLockedAttribute($attributeCode)) {
            unset($this->_lockedAttributes[$attributeCode]);
        }

        return $this;
    }

    /**
     * Unlock all attributes
     *
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unlockAttributes()
    {
        $this->_lockedAttributes = array();
        return $this;
    }

    /**
     * Retrieve locked attributes
     *
     * @return array
     */
    public function getLockedAttributes()
    {
        $lockedAttributes = array_keys($this->_lockedAttributes);
        return $lockedAttributes;
    }

    /**
     * Checks that model have locked attributes
     *
     * @return boolean
     */
    public function hasLockedAttributes()
    {
        return !empty($this->_lockedAttributes);
    }

    /**
     * Retrieve locked attributes
     *
     * @return boolean
     */
    public function isLockedAttribute($attributeCode)
    {
        $isLockedAttr = isset($this->_lockedAttributes[$attributeCode]);
        return $isLockedAttr;
    }

    /**
     * @param  string|array $cskey
     * @param  mixed        $value
     * @param  boolean      $isChanged
     * @return Varien_Object
     */
    public function setData($cskey, $value = null)
    {
        if ($this->hasLockedAttributes()) {
            if (is_array($cskey)) {
                foreach ($this->getLockedAttributes() as $attribute) {
                    if (isset($cskey[$attribute])) {
                        unset($cskey[$attribute]);
                    }
                }
            } elseif ($this->isLockedAttribute($cskey)) {
                return $this;
            }
        } elseif ($this->isReadonly()) {
            return $this;
        }
        return parent::setData($cskey, $value);
    }

    /**
     * Unset data from the object.
     *
     * $cskey can be a string only. Array will be ignored.
     *
     * $isChanged will specify if the object needs to be saved after an update.
     *
     * @param  string  $cskey
     * @param  boolean $isChanged
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function unsetData($cskey = null)
    {
        if ((!is_null($cskey) && $this->isLockedAttribute($cskey)) 
            || $this->isReadonly()
        ) {
            return $this;
        }

        return parent::unsetData($cskey);
    }


    /**
     * Get collection instance for Marketplace
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getResourceCollection()
    {
        $collection = parent::getResourceCollection();
        return $collection;
    }


    /**
     * Load entity by attribute
     * @param $attribute
     * @param $value
     * @param string $additionalAttributes
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByAttribute($attribute, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addAttributeToSelect($additionalAttributes)
            ->addAttributeToFilter($attribute, $value)
            ->setPage(1, 1);
        
        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }
    

    /**
     * Load entity by attribute
     * @param $field
     * @param $value
     * @param string $additionalAttributes
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByField($field, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($additionalAttributes);
        if(is_array($field) && is_array($value)) {
            foreach($field as $key=>$f) {
                if(isset($value[$key])) {
                    $collection->addFieldToFilter($f, $value[$key]);
                }
            }
        } else {
            $collection->addFieldToFilter($field, $value);
        }
        
        $collection->setCurPage(1)
            ->setPageSize(1);
        foreach ($collection as $object) {
            $this->load($object->getId());
            return $this;
        }
        return $this;
    }
    

    /**
     * Check for empty values for provided Attribute Code on each entity
     * @param string $attributeCode
     * @param array $entityIds
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateMassAttribute($attributeCode = '',array $entityIds)
    {
        $csCollection = $this->getResourceCollection()
            ->addAttributeToSelect($attributeCode)
            ->addAttributeToFilter('entity_id', array('in'=>$entityIds));
        if (count($csCollection)) {
            $this->_massCollection = $csCollection;
            foreach ($csCollection as $model) {
                if (!strlen($model->getData($attributeCode))) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }


    /**
     * Check for empty values for provided Attribute Code on each entity
     * @param array $entityIds
     * @param array $attribute
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMassAttribute(array $entityIds, array $attribute)
    {
        if(!isset($attribute['code']) || !isset($attribute['value'])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('New values was missing.')
            );
        }
        if($this->_massCollection == null) {
            $collection = $this->getResourceCollection()
                ->addAttributeToSelect($attribute['code'])
                ->addAttributeToFilter('entity_id', array('in'=>$entityIds));
        } else {
            $collection = $this->_massCollection;
        }
        if (count($collection)) {
            $this->_massCollection = $collection;
            foreach ($collection as $model) {
                $model->load($model->getId());
                $model->setData($attribute['code'], $attribute['value'])->save();
            }
            return true;
        }
        return null;
    }
    /**
     * Retrieve sore object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($this->getStoreId());
    }

    /**
     * Retrieve all store ids of object current website
     *
     * @return array
     */
    public function getWebsiteStoreIds()
    {
        $websiteStoreIds = $this->getStore()->getWebsite()->getStoreIds(true);
        return $websiteStoreIds;
    }

    /**
     * Adding attribute code and value to default value registry
     *
     * Default value existing is flag for using store value in data
     *
     * @param  string $attributeCode
     * @value  mixed  $value
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setAttributeDefaultValue($attributeCode, $value)
    {
        $this->_defaultValues[$attributeCode] = $value;
        return $this;
    }

    /**
     * Retrieve default value for attribute code
     *
     * @param  string $attributeCode
     * @return array|boolean
     */
    public function getAttributeDefaultValue($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_defaultValues) ? $this->_defaultValues[$attributeCode] : false;
    }

    /**
     * Set attribute code flag if attribute has value in current store and does not use
     * value of default store as value
     *
     * @param  string $attributeCode
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setExistsStoreValueFlag($attributeCode)
    {
        $this->_storeValuesFlags[$attributeCode] = true;
        return $this;
    }

    /**
     * Check if object attribute has value in current store
     *
     * @param  string $attributeCode
     * @return bool
     */
    public function getExistsStoreValueFlag($attributeCode)
    {
        return array_key_exists($attributeCode, $this->_storeValuesFlags);
    }

    /**
     * Before save unlock attributes
     *
     * @return Ced_CsMarketplace_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->unlockAttributes();
        return parent::_beforeSave();
    }

    /**
     * Checks model is deletable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deletable flag
     *
     * @param  boolean $value
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool) $value;
        return $this;
    }

    /**
     * Checks model is deletable
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is deletable flag
     *
     * @param  boolean $value
     * @return Ced_CsMarketplace_Model_Abstract
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool)$value;
        return $this;
    }
    
    /**
     * Server side validation classes
     */
    public function zendValidate($field, $value,$fieldvalue, $classes = '',$isRequired = false) 
    {
        $classes = explode(' ', trim($classes));
        $errors = array();
        if(is_array($fieldvalue)) {
            $fieldvalue=isset($fieldvalue['value'])?$fieldvalue['value']:count($fieldvalue) > 0?implode(',', $fieldvalue):'';
        }
        if(is_array($classes) && count($classes) > 0 && strlen($fieldvalue)) {    
            foreach ($classes as $class) {
                $class = trim($class);
                switch($class) {
                case 'validate-url'     :
                case 'validate-shopurl' : $availability =  $this->checkAvailability(array('shop_url'=>trim($fieldvalue)));

                    if(isset($availability['success']) && $availability['success'] == 0) {
                        if(isset($availability['message']) && strlen($availability['success']) > 0) {
                            $errors[] = $availability['message'];
                        } else {
                            $errors[] = __('Please enter a valid Shop URL Key. For example "my-shop-url".');
                        }
                    }
                                             
                    break;
                case 'validate-email'   : if (!\Zend_Validate::is($fieldvalue, 'EmailAddress')) {
                        $errors[] = __('Invalid email address "%s" in "%s" Field.', $fieldvalue, $field);
                }
                    break;
                case 'validate-digits'  : 
                case 'validate-number' : if (!\Zend_Validate::is($fieldvalue, 'Digits')) {
                        $errors[] = __('"%s" must contain only numbers.', $field);
                }
                    break;
                case 'validate-alpha'  : if (!\Zend_Validate::is($fieldvalue, 'Alpha')) {
                        $errors[] = __('"%s" contains non alphabetic characters', $field);
                }
                    break;
                case 'validate-alphanum'  : if (!\Zend_Validate::is($fieldvalue, 'Alnum')) {
                        $errors[] = __('"%s" contains characters which are non alphabetic and no digits', $field);
                }
                    break;
                case 'validate-no-html-tags' : if(preg_match('/<(\/)?\w+/', $fieldvalue)) {
                        $errors[] =  __('HTML tags are not allowed').' '. __('in "%s" Field.', $field);
                } 
                    break;
                }
            }
        }
        
       if ($isRequired && trim($fieldvalue) == '') {
    		$errors[] = __($field.' is a required field.');
    	}
        return $errors;
    }
    
    /**
     * Check Shop Url availability
     */
    public function checkAvailability($venderData = array(),$id = 0) 
    {
        if(!$id && $this->getId()) { $id = $this->getId(); 
        }
        $rawShopUrl = isset($venderData['shop_url']) && $venderData['shop_url'] ? $venderData['shop_url'] : '';
        $rawShopUrl = str_replace('"', '', $rawShopUrl);
        /* remove the slash amke a empty url there */    
        $shopUrl = $this->formatShopUrl($rawShopUrl);
        $json = array('success'=>0,'message'=>__('Please enter a valid Shop URL Key. For example "my-shop-url".'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');
        if (strlen($shopUrl) && preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $shopUrl)) {    
            $vendor = $this->loadByAttribute('shop_url', $shopUrl);
            $json = array('success'=>0,'message'=>__('Shop Url is not available.'),'shop_url'=>$shopUrl, 'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');
            if($rawShopUrl != $shopUrl) {
                $json = array('success'=>0,'message'=>__('Please enter a valid Shop URL Key. For example "my-shop-url".'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion' => __('Suggested Shop URL').' : '.$shopUrl.'');
            } elseif($id) {
                if ((!$vendor || !$vendor->getId()) || ($vendor && $vendor->getId() && $vendor->getId() == $id)) {
                    $json = array('success'=>1,'message'=>__('Shop Url is available'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');    
                }
            } else {
                if(!$vendor || !$vendor->getId()) {
                    $json = array('success'=>1,'message'=>__('Shop Url is available'),'shop_url' => $shopUrl,'raw_shop_url'=>$rawShopUrl,'suggestion'=>'');    
                }
            }
        }
        return $json;    
        
    }
    
    /**
     * Get unused path for vendor shop url
     */
    public function getUnusedPath($cedrequestPath = '', $count = 0, $suffix = '')
    {
        $tempPath = $cedrequestPath;
        if(strlen($cedrequestPath) == 0) return $cedrequestPath;
        if($count) $cedrequestPath = $cedrequestPath . '-' . $count;
        $existModel = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite')
                           ->getCollection()
                           ->addFieldToFilter('request_path',array('eq'=>$cedrequestPath.$suffix))
                           ->getFirstItem();
        if($existModel && $existModel->getId()) {
            return $this->getUnusedPath($tempPath,$count+1);
        }
        return  $cedrequestPath;
    }

    /**
     * Get formated Shop Url
     */
    function formatShopUrl($shopUrl = '') 
    {
        return strtolower(trim(str_replace('.', '', preg_replace('#[^0-9a-z-]+#i', '', $this->_url->formatUrlKey($shopUrl)))));
    }
}
