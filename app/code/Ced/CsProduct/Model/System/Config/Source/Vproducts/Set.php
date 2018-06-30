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

namespace Ced\CsProduct\Model\System\Config\Source\Vproducts;

class Set extends \Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Set
{
    /**
     * @param bool $defaultValues
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionArray($defaultValues = false, $withEmpty = false)
    {
        $options = array();

        $sets = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter($this->_objectManager->get('Magento\Catalog\Model\Product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();
        if (!$defaultValues)
            $allowedSet = $this->getAllowedSet($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());

        $vendorSets = $this->getVendorCreatedSets();
        foreach ($sets as $value => $label) {
            if ((!$defaultValues && !in_array($value, $allowedSet)) || in_array($value, $vendorSets)) continue;
            $options[] = array('value' => $value, 'label' => $label);
        }
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }

        return $options;
    }

    public function getVendorCreatedSets()
    {
        $data = array();
        $set = $this->_objectManager->get('Ced\CsVendorProductAttribute\Model\Attributeset')->getCollection()->getData();
        foreach ($set as $value) {
            $data[] = $value['attribute_set_id'];
        }
        return $data;
    }
}
