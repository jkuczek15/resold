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
 
namespace Ced\CsMarketplace\Model\ResourceModel;
 
class Vendor extends \Magento\Eav\Model\Entity\AbstractEntity
{
	protected $_objectManager;
    /**
     * @param \Magento\Eav\Model\Entity\Context                  $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Validator\Factory               $validatorFactory
     * @param \Magento\Framework\Stdlib\DateTime                 $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param array                                              $data
     */
            
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Validator\Factory $validatorFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->_validatorFactory = $validatorFactory;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->_objectManager=$objectManager;
        $this->setType('csmarketplace_vendor');
        $this->setConnection('csmarketplace_vendor_read', 'csmarketplace_vendor_write');
    }
        
    
    public function getMainTable()
    {
        return $this->getTable('ced_csmarketplace_vendor');
    }
    
    public function deleteFromGroup(\Magento\Framework\Model\AbstractModel $vendor)
    {
        if ($vendor->getId() <= 0 ) {
            return $this;
        }
        if (strlen($vendor->getGroup()) <= 0 ) {
            return $this;
        }
        $vendorGroup = $this->_objectManager->get('Ced\CsGroup\Model\Group')->loadByField('group_code', $vendor->getGroup());
        $dbh = $this->getConnection();
      
        $condition = "`{$this->getTable('ced_csgroup_vendor_group')}`.vendor_id = " . $dbh->quote($vendor->getId())
            . " AND `{$this->getTable('ced_csgroup_vendor_group')}`.parent_id = " . $dbh->quote($vendorGroup->getGroupId());
       
        $dbh->delete($this->getTable('ced_csgroup_vendor_group'), $condition);
        return $this;
    }
    
    public function groupVendorExists(\Magento\Framework\Model\AbstractModel $vendor)
    {
        if ($vendor->getId() > 0 ) {
            $groupTable = $this->getTable('ced_csgroup_vendor_group');
            
            $vendorGroup = $this->_objectManager->get('Ced\CsGroup\Model\Group')
                            ->loadByField('group_code', $vendor->getGroup());
          
            $dbh = $this->getConnection();
            $select = $dbh->select()->from($groupTable)
                    ->where("parent_id = {$vendorGroup->getGroupId()} AND vendor_id = {$vendor->getId()}");
            return $dbh->fetchCol($select);
        } else {
            return [];
        }
    }
    
    public function add(\Magento\Framework\Model\AbstractModel $vendor)
    {
    	$dbh = $this->getConnection();
        $aGroups = $this->hasAssigned2Group($vendor);
        if (sizeof($aGroups) > 0 ) {
            foreach($aGroups as $idx => $data){
                $dbh->delete($this->getTable('ced_csgroup_vendor_group'), "group_id = {$data['group_id']}");
            }
        }

        if (strlen($vendor->getGroup()) > 0) {
            $group = $this->_objectManager->get('Ced\CsGroup\Model\Group')->loadByField('group_code', $vendor->getGroup());
        } else {
            $group = new \Magento\Framework\DataObject();
            $group->setTreeLevel(0);
        }
           $dbh->insert(
               $this->getTable('ced_csgroup_vendor_group'), array(
               'parent_id' => $group->getId(),
               'tree_level'=> ($group->getTreeLevel() + 1),
               'sort_order'=> 0,
               'group_type' => 'U',
               'vendor_id'  => $vendor->getId(),
               'group_code' => $vendor->getGroup(),
               'group_name' => $vendor->getName()
               )
           );

           return $this;
    }
    
    public function hasAssigned2Group($vendor)
    {
        if (is_numeric($vendor)) {
            $vendorId = $vendor;
        } else if ($vendor instanceof \Magento\Framework\Model\AbstractModel) {
            $vendorId = $vendor->getId();
        } else {
            return null;
        }

        if ($vendorId > 0 ) {
        	$dbh=$this->getConnection();
            $select = $dbh->select();
            $select->from($this->getTable('ced_csgroup_vendor_group'))
                ->where("parent_id > 0 AND vendor_id = {$vendorId}");
            return $dbh->fetchAll($select);
        } else {
            return null;
        }
    }
    
    public function vendorExists(\Magento\Framework\Model\AbstractModel $vendor)
    {
        $vendorsTable = $this->getTable('admin/vendor');
        $db = $this->_getReadAdapter();

        $select = $db->select()
                ->from(array('u' => $vendorsTable))
                ->where('u.vendor_id != ?', (int) $vendor->getId())
                ->where('u.vendorname = :vendorname OR u.email = :email');
        $row = $db->fetchRow(
            $select, array(
                ':vendorname' => $vendor->getVendorname(),
                ':email'    => $vendor->getVendorname(),
            )
        );
        return $row;
    }
    
  
}
