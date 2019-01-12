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
 * @package     Ced_Fyndiq
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\FbNative\Block\Adminhtml\Form\Field;

use Magento\Framework\Api\SearchCriteriaBuilder;


/**
 * HTML select element block with customer groups options
 */
class MagentoAttributes extends \Magento\Framework\View\Element\Html\Select
{
    private  $searchCriteriaBuilder;

    private  $collection;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Ced\FbNative\Model\Source\FbAttribute\FbAttributes $fbAttr,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collection = $collection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }


    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId  return name by customer group id
     * @return array|string
     */
    protected function _getAttributes()
    {
        $attributes = $this->collection->getItems();

        $mattributecode = '--please select--';
        $magentoattributeCodeArray[''] = $mattributecode;
        foreach ($attributes as $attribute){
            $magentoattributeCodeArray[$attribute->getAttributecode()] = $attribute->getAttributecode();
        }
        return $magentoattributeCodeArray;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getAttributes() as $code) {
                $this->addOption($code, addslashes($code));
            }
        }
        return parent::_toHtml();
    }
}
