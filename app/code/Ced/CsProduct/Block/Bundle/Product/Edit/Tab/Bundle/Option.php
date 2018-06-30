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

namespace Ced\CsProduct\Block\Bundle\Product\Edit\Tab\Bundle;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Option extends \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option
{
    //protected $_template = 'Magento_Bundle::product/edit/bundle/option.phtml';

    protected $_template = 'Ced_CsProduct::bundle/product/edit/bundle/option.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Bundle\Model\Source\Option\Type $optionTypes,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_optionTypes = $optionTypes;
        $this->_yesno = $yesno;
        parent::__construct($context,$yesno,$optionTypes,$registry,$data);
        //$this->setData('area','adminhtml');
    }

    protected function _construct()
    {
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }


    /**
     * Retrieve Product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_selection_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'id' => $this->getFieldId() . '_<%- data.index %>_add_button',
                'label' => __('Add Products to Option'),
                'class' => 'add add-selection'
            ]
        );

        $this->addChild(
            'close_search_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'id' => $this->getFieldId() . '_<%- data.index %>_close_button',
                'label' => __('Close'),
                'on_click' => 'bSelection.closeSearch(event)',
                'class' => 'back no-display'
            ]
        );

        $this->addChild(
            'option_delete_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Delete Option'), 'class' => 'action-delete', 'on_click' => 'bOption.remove(event)']
        );

        $this->addChild(
            'selection_template',
            'Ced\CsProduct\Block\Bundle\Product\Edit\Tab\Bundle\Option\Selection'
        );
        return $this;
        //return parent::_prepareLayout();
    }

    /**
     * Retrieve list of bundle product options
     *
     * @return array
     */
    public function getOptions()
    {
        if (!$this->_options) {
            /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionCollection */
            $optionCollection = $this->getProduct()->getTypeInstance()->getOptionsCollection($this->getProduct());

            $selectionCollection = $this->getProduct()->getTypeInstance()->getSelectionsCollection(
                $this->getProduct()->getTypeInstance()->getOptionsIds($this->getProduct()),
                $this->getProduct()
            );

            $this->_options = $optionCollection->appendSelections($selectionCollection);
            if ($this->getCanReadPrice() === false) {
                foreach ($this->_options as $option) {
                    if ($option->getSelections()) {
                        foreach ($option->getSelections() as $selection) {
                            $selection->setCanReadPrice($this->getCanReadPrice());
                            $selection->setCanEditPrice($this->getCanEditPrice());
                        }
                    }
                }
            }
        }
        return $this->_options;
    }

    /**
     * @return mixed
     */
    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()->getBlock('admin.product.bundle.items')->getChildBlock('add_button')->getId();
        return $buttonId;
    }

    /**
     * @return mixed
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id' => $this->getFieldId() . '_<%- data.index %>_type',
                'class' => 'select select-product-option-type required-option-select',
                'extra_params' => 'onchange="bOption.changeType(event)"',
            ]
        )->setName(
            $this->getFieldName() . '[<%- data.index %>][type]'
        )->setOptions(
            $this->_optionTypes->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * @return mixed
     */
    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            ['id' => $this->getFieldId() . '_<%- data.index %>_required', 'class' => 'select']
        )->setName(
            $this->getFieldName() . '[<%- data.index %>][required]'
        )->setOptions(
            $this->_yesno->toOptionArray()
        );

        return $select->getHtml();
    }

    /**
     * @return bool
     */
    public function isDefaultStore()
    {
        return $this->getProduct()->getStoreId() == '0';
    }
}
