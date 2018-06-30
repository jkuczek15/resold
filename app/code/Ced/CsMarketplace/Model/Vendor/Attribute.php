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
 
namespace Ced\CsMarketplace\Model\Vendor;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
 
 
class Attribute extends \Magento\Eav\Model\Entity\Attribute
{
    /**
     * Prefix of vendor attribute events names
     *
     * @var string
     */
    protected $_eventPrefix='csmarektplace_vendor_attribute';
    
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    protected $_setup;
    
    protected $_objectManager;
    
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Catalog\Model\Product\ReservedAttributeList $reservedAttributeList,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $eavConfig,
            $eavTypeFactory,
            $storeManager,
            $resourceHelper,
            $universalFactory,
            $optionDataFactory,
            $dataObjectProcessor,
            $dataObjectHelper,
            $localeDate,
            $reservedAttributeList,
            $localeResolver,
            $dateTimeFormatter,
            null,
            null,
            $data
        );
       
        $this->_objectManager = $objectManager;
        $this->setEntityTypeId($this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getEntityTypeId());
        $setIds = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')->setEntityTypeFilter($this->getEntityTypeId())->getAllIds();
        $this->setAttributeSetIds($setIds);
        
    }
    

    /**
     * Set store scope
     *
     * @param  int|string|Mage_Core_Model_Store $store
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStore($store) 
    {
        $this->setStoreId($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($store)->getId());
        return $this;
    }

    /**
     * Set store scope
     *
     * @param  int|string|Mage_Core_Model_Store $storeId
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId) 
    {
        if ($storeId instanceof \Magento\Store\Model\StoreManagerInterface) {
            $storeId = $storeId->getId();
        }
        $this->_storeId =(int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId() 
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId() 
    {
        return \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID;
    }
    
    /**
     * Load vendor's attributes into the object
     *
     * @param  Mage_Core_Model_Abstract $object
     * @param  integer                  $entityId
     * @param  array|null               $attributes
     * @return Ced_CsMarketplace_Model_Vendor_Attribute
     */
    public function load($entityId, $field = null) 
    {
        parent::load($entityId, $field);
        if($this && $this->getId()) {            
            $joinFields=$this->_vendorForm($this);
            if(count($joinFields) > 0) {
                foreach($joinFields as $joinField) {
                    $this->setData('is_visible', $joinField->getIsVisible());
                    $this->setData('position', $joinField->getSortOrder());
                    $this->setData('use_in_registration', $joinField->getData('use_in_registration'));
                    $this->setData('position_in_registration', $joinField->getData('position_in_registration'));
                    $this->setData('use_in_left_profile', $joinField->getData('use_in_left_profile'));
                    $this->setData('fontawesome_class_for_left_profile', $joinField->getData('fontawesome_class_for_left_profile'));
                    $this->setData('position_in_left_profile', $joinField->getData('position_in_left_profile'));
                }
            }
        }
        return $this;
    }
    
    public function _vendorForm($attribute) 
    {
        $store = $this->getStoreId();        
        $fields = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')
            ->getCollection()
            ->addFieldToFilter('attribute_id', array('eq'=>$attribute->getAttributeId()))
            ->addFieldToFilter('attribute_code', array('eq'=>$attribute->getAttributeCode()))
            ->addFieldToFilter('store_id', array('eq'=>$store));
        if(count($fields) == 0) {
            $data[]=array(
            'attribute_id' => $attribute->getId(),
            'attribute_code' => $attribute->getAttributeCode(),
            'is_visible'   => 0,
            'sort_order'   => 0,
            'store_id'       => $store,
            'use_in_registration' => 0,
            'position_in_registration' => 0,
            'use_in_left_profile' => 0,
            'fontawesome_class_for_left_profile' => 'fa fa-circle-thin',
            'position_in_left_profile' => 0,
            );
            $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->insertMultiple($data);
            return $this->_vendorForm($attribute);
        }
        return $fields;
    }
    
    /**
     * Retrive Vendor attribute collection
     *
     * @return Mage_Eav_Model_Resource_Entity_Collection
     */
     public function getCollection() {
		$collection = parent::getCollection();
		$typeId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getEntityTypeId();
		$collection = $collection->addFieldToFilter('entity_type_id',array('eq'=>$typeId));
		$labelTableName = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection')->getTableName('eav_attribute_label');
		
		$tableName = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection')->getTableName('ced_csmarketplace_vendor_form_attribute');
		if($this->getStoreId()) {
		$availableStoreWiseIds=$this->getStoreWiseIds($this->getStoreId());
			$collection->getSelect()->join(array('vform'=>$tableName), 'main_table.attribute_id=vform.attribute_id', array('is_visible'=>'vform.is_visible','sort_order'=>'vform.sort_order','store_id'=>'vform.store_id','use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile','position_in_registration'=>'vform.position_in_registration', 'position_in_left_profile'=>'vform.position_in_left_profile', 'fontawesome_class_for_left_profile'=>'vform.fontawesome_class_for_left_profile'));
			$collection->getSelect()->where('(vform.attribute_id IN ("'.$availableStoreWiseIds.'") AND vform.store_id='.$this->getStoreId().') OR (vform.attribute_id NOT IN ("'.$availableStoreWiseIds.'") AND vform.store_id=0)');
			$collection->getSelect()->group('vform.attribute_id');
			$collection->getSelect()->joinLeft(array('vlabel'=>$labelTableName), 'main_table.attribute_id=vlabel.attribute_id && vlabel.store_id='.$this->getStoreId(), array('store_label'=>'vlabel.value'));
		} else {
			$collection->getSelect()->join(array('vform'=>$tableName), 'main_table.attribute_id=vform.attribute_id && vform.store_id=0', array('is_visible'=>'vform.is_visible','sort_order'=>'vform.sort_order','store_id'=>'vform.store_id','use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile','position_in_registration'=>'vform.position_in_registration', 'position_in_left_profile'=>'vform.position_in_left_profile', 'fontawesome_class_for_left_profile'=>'vform.fontawesome_class_for_left_profile'));
			$collection->getSelect()->joinLeft(array('vlabel'=>$labelTableName), 'main_table.attribute_id=vlabel.attribute_id && vlabel.store_id=0', array('store_label'=>'vlabel.value'));
		}
		return $collection;
	}
    
    public function getStoreWiseIds($storeId=0) 
    {
        if($storeId) {
            $allowed=array();
            foreach($this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->getCollection()
                ->addFieldToFilter('store_id', array('eq'=>$storeId))
            as $attribute) {
                $allowed[]=$attribute->getAttributeId();
            }
            return implode(',', $allowed);
        }
        return array();
    }
        
    
    
    public function addToGroup($group=array()) 
    {
        if (count($group) > 0) {
            $setIds = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')
                ->setEntityTypeFilter($this->getEntityTypeId())->getAllIds();
            $setId = isset($setIds[0])?$setIds[0]:$this->getEntityTypeId();
           $installer = $this->_objectManager->create('Ced\CsMarketplace\Setup\CsMarketplaceSetup');
           //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
           //$this->_setup = $objectManager->create('Magento\Framework\Setup\ModuleDataSetupInterface');
           //$this->_setup->startSetup();
            
            if(!in_array($group, $this->getGroupOptions($setId, true))) {
                $installer->addAttributeGroup(
                    'csmarketplace_vendor',
                    $setId,
                    $group
                );
            }
            $installer->addAttributeToGroup(
                'csmarketplace_vendor',
                $setId,
                $group, //Group Name
                $this->getAttributeId()
            );
           //$this->_setup->endSetup();
        }
    }
    
    protected function getGroupOptions($setId,$flag=false) 
    {
        $groupCollection = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection')
            ->setAttributeSetFilter($setId);

        $groupCollection->setSortOrder()->load();
        $options=array();
        if($flag) {
            foreach ($groupCollection as $group) {
                $options[]=$group->getAttributeGroupName();        
            }
        } else {
            foreach ($groupCollection as $group) {
                $options[$group->getId()]=$group->getAttributeGroupName();        
            }
        }
        return     $options;
    }
    
    public function delete() 
    {
        if ($this->getId()) {
            $joinFields=$this->_vendorForm($this);
            if(count($joinFields) > 0) {
                foreach($joinFields as $joinField) {
                    $joinField->delete();
                }
            }
        }
        return parent::delete();;
    }
    
    /**
     * Processing vendor attribute after save data
     *
     * @return Ced_CsMarketplace_Model_Vendor_Attribute
     */
    public function afterSave() 
    {
        parent::afterSave();
        if ($this->getId()) {
            $joinFields=$this->_vendorForm($this);
            if(count($joinFields) > 0) {
                foreach($joinFields as $joinField) {
                    $joinField->setData('is_visible', $this->getData('is_visible'));
                    $joinField->setData('sort_order', $this->getData('position'));
                    $joinField->setData('use_in_registration', $this->getData('use_in_registration'));
                    $joinField->setData('position_in_registration', $this->getData('position_in_registration'));
                    $joinField->setData('use_in_left_profile', $this->getData('use_in_left_profile'));
                    $joinField->setData('fontawesome_class_for_left_profile', $this->getData('fontawesome_class_for_left_profile'));
                    $joinField->setData('position_in_left_profile', $this->getData('position_in_left_profile'));
                    $joinField->save();
                }
            }
        }
        return $this;
    }    
}
