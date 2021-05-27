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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vendor\Profile;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class View extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    public $_vendor;
    public $_totalattr;
    public $_savedattr;
    public $_objectManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory
    )
    {
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
        $this->_objectManager = $objectManager;
        $this->_vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor');
        $this->_totalattr = 0;
        $this->_savedattr = 0;
    }

    public function getGroup($country_id)
    {
        return $country_id;
    }

    public function getCountryIdValue($country_id)
    {
        $regionCollection = $this->_objectManager->create('Magento\Directory\Model\Region')->getCollection()->addCountryFilter($country_id);
        if ($regionCollection->getData() != null) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function getRegionFromId($region_id)
    {

        $regionName = "";
        foreach ($this->_objectManager->create('Magento\Directory\Model\Region')->getCollection() as $region) {
            if ($region->getData('region_id') == $region_id){
                $regionName = $region->getData('default_name');
                break;
            }
        }
        return $regionName;
    }

    public function getCountryId($cid)
    {
        $country = $this->_objectManager->create('Magento\Directory\Model\Country')->loadByCode($cid);
        return $country->getName();
    }

    public function getMediaUrl()
    {
        return $this->_objectManager->get('Ced\CsMarketplace\Model\Url')->getMediaUrl();
    }

    /**
     * Preparing collection of vendor attribute group vise
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection
     */
    public function getVendorAttributeInfo()
    {

        $entityTypeId = $this->_vendor->getEntityTypeId();

        $setIds = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter($entityTypeId)->getAllIds();

        $groupCollection = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection');
        if (count($setIds) > 0) {
            $groupCollection->addFieldToFilter('attribute_set_id', array('in' => $setIds));
        }

        $groupCollection->setSortOrder()->load();

        $vendor_info = $this->_vendor->load($this->getVendorId());
        $total = 0;
        $saved = 0;

        foreach ($groupCollection as $group) {
            $attributes = $this->_vendor->getAttributes($group->getId(), true);
            if (count($attributes) == 0) {
                continue;
            }
            foreach ($attributes as $attr) {
                /* $attribute = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')->load($attr->getId());
                 if($attribute->getIsVisible()){
                    $total++;
                    if($vendor_info->getData($attr->getAttributeCode())){
                        $saved++;
                    }
                } */
            }
        }

        $this->_totalattr = $total;
        $this->_savedattr = $saved;
        return $groupCollection;
    }


}
