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

namespace Ced\CsMarketplace\Block\Vendor\Navigation;
 
class Statatics extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	protected $_vendor;
	protected $_totalattr = 0;
	protected $_savedattr = 0;
	
	
	public function getTotalAttr() {
		return $this->_totalattr;
	}
	
	public function getSavedAttr() {
		return $this->_savedattr;
	}
	
	/**
     * Preparing collection of vendor attribute group vise
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection
     */
	public function getVendorAttributeInfo() {
		$this->_totalattr = 0;
		$this->_savedattr = 0;
		$this->_vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor');
		$entityTypeId  = $this->_vendor->getEntityTypeId();
		$setIds = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute\Set')
							->getCollection()
				->setEntityTypeFilter($entityTypeId)->getAllIds();
				
		$groupCollection =  $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute\Group')
							->getCollection();
		if(count($setIds) > 0) {
			$groupCollection->addFieldToFilter('attribute_set_id',array('in'=>$setIds));
		}
		$groupCollection->setSortOrder()->load();
				
		$vendor_info = $this->_vendor->load($this->getVendorId());
		$total = 0;
		$saved = 0;
		foreach($groupCollection as $group){
			$attributes = $this->_vendor->getAttributes($group->getId(), true);
			if (count($attributes)==0) {
				continue;
			}
			
			foreach ($attributes as $attr){
				$attribute = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')->setStoreId(0)->load($attr->getid());
				if(!$attribute->getisVisible()) continue;
				$total += 1;
				if($vendor_info->getData($attr->getAttributeCode())){
					$saved++;
				}
			}
		}
		$this->_totalattr = $total-1; 
		$this->_savedattr = $saved;	
		
		return $groupCollection;
	}
	
}
