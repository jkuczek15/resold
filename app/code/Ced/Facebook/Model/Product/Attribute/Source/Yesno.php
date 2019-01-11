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
 * @category  Ced
 * @package   Ced_Facebook
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Facebook\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Yesno
 * @package Ced\Facebook\Model\Product\Attribute\Source
 */
class Yesno extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Product Status values
     */
    const STATUS_DISABLED = 2;
    const STATUS_ENABLED = 1;

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $results = [];
        foreach (self::getOptionArray() as $index => $value) {
            $results[] = ['value' => $index, 'label' => $value];
        }

        return $results;
    }

    /**
     * @param int|string $optionId
     * @return null|string
     */
    public function getOptionText($optionId)
    {
        $option = self::getOptionArray();

        return isset($option[$optionId]) ? $option[$optionId] : null;
    }

    /**
     * Retrieve Saleable Status Ids
     * Default Product Enable status
     *
     * @return int[]
     */
    public function getSaleableStatusIds()
    {
        return [self::STATUS_ENABLED];
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [self::STATUS_ENABLED => __('Yes'), self::STATUS_DISABLED => __('No')];
    }

    /**
     * Retrieve Visible Status Ids
     *
     * @return int[]
     */
    public function getVisibleStatusIds()
    {
        return [self::STATUS_ENABLED];
    }

    /**
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param string $dir
     * @return $this
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attrCode = $this->getAttribute()->getattrCode();
        $attributeId = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();
        $linkField = $this->getAttribute()->getEntity()->getLinkField();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tabName = $attrCode . '_t';

            $collection->getSelect()->joinLeft(
                [$tabName => $attributeTable],
                "e.{$linkField}={$tabName}.{$linkField}" .
                " AND {$tabName}.attribute_id='{$attributeId}'" .
                " AND {$tabName}.store_id='0'",
                []
            );

            $valueExpr = $tabName . '.value';
        } else {
            $valTable1 = $attrCode . '_t1';
            $valueTable2 = $attrCode . '_t2';

            $collection->getSelect()->joinLeft(
                [$valTable1 => $attributeTable],
                "e.{$linkField}={$valTable1}.{$linkField}" .
                " AND {$valTable1}.attribute_id='{$attributeId}'" .
                " AND {$valTable1}.store_id='0'",
                []
            )->joinLeft(
                [$valueTable2 => $attributeTable],
                "e.{$linkField}={$valueTable2}.{$linkField}" .
                " AND {$valueTable2}.attribute_id='{$attributeId}'" .
                " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                []
            );

            $valueExpr = $collection->getConnection()->getCheckSql(
                $valueTable2 . '.value_id > 0',
                $valueTable2 . '.value',
                $valTable1 . '.value'
            );
        }
        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
