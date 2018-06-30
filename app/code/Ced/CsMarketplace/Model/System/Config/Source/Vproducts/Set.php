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

namespace Ced\CsMarketplace\Model\System\Config\Source\Vproducts;
 
class Set extends \Ced\CsMarketplace\Model\System\Config\Source\AbstractBlock
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($defaultValues = false,$withEmpty = false)
    {
        $options = array();
        $sets = $this->_objectManager->get('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')
        ->setEntityTypeFilter($this->_objectManager->get('Magento\Catalog\Model\Product')->getResource()->getTypeId())
        ->load()
        ->toOptionHash();
        if (!$defaultValues) {
            $allowedSet = $this->getAllowedSet($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId()); 
        }
        foreach($sets as $value=>$label) {
            if(!$defaultValues && !in_array($value, $allowedSet)) { continue; 
            }
            $options[] = array('value'=>$value,'label'=>$label);
        }
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
        return $options;
    }
    
    /**
     * Get Allowed product attribute set
     */
    public function getAllowedSet($storeId = 0) 
    {
        if($storeId) { return explode(',', $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/general/set', $storeId)); 
        }
        return explode(',', $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/general/set'));
    }

}
