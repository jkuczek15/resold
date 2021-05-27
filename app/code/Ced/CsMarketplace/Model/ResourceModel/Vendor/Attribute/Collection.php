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
 
namespace Ced\CsMarketplace\Model\ResourceModel\Vendor\Attribute;
class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    /**
     * @var \Magento\Eav\Model\Entity
     */
    protected $eavEntity;
    
    public function __construct(
        \Magento\Eav\Model\Entity $eavEntity
    ) {
        $this->eavEntity = $eavConfig;
        parent::__construct();
    }
    
    
    /**
     * Resource model initialization
     */
    protected function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\Vendor\Attribute', 'Magento\Eav\Model\ResourceModel\Entity\Attribute');
    }

   /**
     * initialize select object
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)$this->eavEntity->setType('csmarketplace_vendor')->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = array();
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == \Magento\Framework\DB\Ddl\Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = Magento::getResourceHelper('core')->castField('main_table.'.$labelColumn);
            }
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
            ->join(
                array('additional_table' => $this->getTable('csmarketplace/vendor_form')),
                'additional_table.attribute_id = main_table.attribute_id'
            )
            ->where('main_table.entity_type_id = ?', $entityTypeId);

    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $typeId
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return array
     */
    protected function _getLoadDataFields()
    {
        $fields = array_merge(
            parent::_getLoadDataFields(),
            array(
                'additional_table.is_global',
                'additional_table.is_html_allowed_on_front',
                'additional_table.is_wysiwyg_enabled'
            )
        );

        return $fields;
    }
}
