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

namespace Ced\CsMarketplace\Model\ResourceModel\Vendor;

class Attribute extends \Magento\Eav\Model\ResourceModel\Entity\Attribute
{

    /**
     * Perform actions before object save
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @return Magento\Catalog\Model\ResourceModel\Attribute
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_clearUselessAttributeValues($object);
        return parent::_afterSave($object);
    }

    /**
     * Clear useless attribute values
     * @return Magento\Catalog\Model\ResourceModel\Attribute
     */
    protected function _clearUselessAttributeValues(\Magento\Framework\Model\AbstractModel $object)
    {
        $origData = $object->getOrigData();

        if ($object->isScopeGlobal()
            && isset($origData['is_global'])
            && \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL != $origData['is_global']
        ) {
            $attributeStoreIds = array_keys($this->_storeManager->getStores());
            if (!empty($attributeStoreIds)) {
                $delCondition = array(
                    'entity_type_id=?' => $object->getEntityTypeId(),
                    'attribute_id = ?' => $object->getId(),
                    'store_id IN(?)'   => $attributeStoreIds
                );
                $this->_getWriteAdapter()->delete($object->getBackendTable(), $delCondition);
            }
        }

        return $this;
    }

    /**
     * Delete entity
     *
     * @return Magento\Catalog\Model\ResourceModel\Attribute
     */
    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function deleteEntity(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getEntityAttributeId()) {
            return $this;
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav_entity_attribute'))
            ->where('entity_attribute_id = ?', (int)$object->getEntityAttributeId());
        $result = $this->_getReadAdapter()->fetchRow($select);

        if ($result) {
            $attribute = $this->_objectManager->get('Magento\Eav\Model\Config')
                ->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $result['attribute_id']);

            if ($this->isUsedBySuperProducts($attribute, $result['attribute_set_id'])) {
                throw new \Exception(__("Attribute '%s' used in configurable products", $attribute->getAttributeCode()));
            }
            $backendTable = $attribute->getBackend()->getTable();
            if ($backendTable) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($attribute->getEntity()->getEntityTable(), 'entity_id')
                    ->where('attribute_set_id = ?', $result['attribute_set_id']);

                $clearCondition = array(
                    'entity_type_id =?' => $attribute->getEntityTypeId(),
                    'attribute_id =?'   => $attribute->getId(),
                    'entity_id IN (?)'  => $select
                );
                $this->_getWriteAdapter()->delete($backendTable, $clearCondition);
            }
        }

        $condition = array('entity_attribute_id = ?' => $object->getEntityAttributeId());
        $this->_getWriteAdapter()->delete($this->getTable('entity_attribute'), $condition);

        return $this;
    }
}

