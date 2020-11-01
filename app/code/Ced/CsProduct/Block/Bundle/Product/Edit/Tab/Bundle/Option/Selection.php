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

namespace Ced\CsProduct\Block\Bundle\Product\Edit\Tab\Bundle\Option;

/**
 * Bundle selection renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Selection extends \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Selection
{
    protected $_template = 'Magento_Bundle::product/edit/bundle/option/selection.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Bundle\Model\Source\Option\Selection\Price\Type $priceType,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
        $this->_priceType = $priceType;
        $this->_yesno = $yesno;
        parent::__construct($context,$yesno,$priceType,$catalogData,$registry,$data);
        $this->setData('area','adminhtml');
    }

    /**
     * Initialize bundle option selection block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }

    /**
     * Prepare block layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'selection_delete_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Delete'), 'class' => 'action-delete', 'on_click' => 'bSelection.remove(event)']
        );
        return $this;
        //return parent::_prepareLayout();
    }

    /**
     * Retrieve price type select html
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id' => $this->getFieldId() . '_<%- data.index %>_price_type',
                'class' => 'select select-product-option-type required-option-select',
            ]
        )->setName(
            $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][selection_price_type]'
        )->setOptions(
            $this->_priceType->toOptionArray()
        );
        if ($this->getCanEditPrice() === false) {
            $select->setExtraParams('disabled="disabled"');
        }
        return $select->getHtml();
    }

    /**
     * Retrieve qty type select html
     *
     * @return string
     */
    public function getQtyTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => $this->getFieldId() . '_<%- data.index %>_can_change_qty', 'class' => 'select']
        )->setName(
            $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][selection_can_change_qty]'
        )->setOptions(
            $this->_yesno->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * Return search url
     *
     * @return string
     */
    public function getSelectionSearchUrl()
    {
        return $this->getUrl('csproduct/bundle_selection/grid');
    }

    /**
     * Check if used website scope price
     *
     * @return string
     */
    public function isUsedWebsitePrice()
    {
        $product = $this->_coreRegistry->registry('product');
        return !$this->_catalogData->isPriceGlobal() && $product->getStoreId();
    }

    /**
     * Retrieve price scope checkbox html
     *
     * @return string
     */
    public function getCheckboxScopeHtml()
    {
        $checkboxHtml = '';
        if ($this->isUsedWebsitePrice()) {
            $fieldsId = $this->getFieldId() . '_<%- data.index %>_price_scope';
            $name = $this->getFieldName() . '[<%- data.parentIndex %>][<%- data.index %>][default_price_scope]';
            $class = 'bundle-option-price-scope-checkbox';
            $label = __('Use Default Value');
            $disabled = $this->getCanEditPrice() === false ? ' disabled="disabled"' : '';
            $checkboxHtml = '<input type="checkbox" id="' .
                $fieldsId .
                '" class="' .
                $class .
                '" name="' .
                $name .
                '"' .
                $disabled .
                ' value="1" />';
            $checkboxHtml .= '<label class="normal" for="' . $fieldsId . '">' . $label . '</label>';
        }
        return $checkboxHtml;
    }
}
