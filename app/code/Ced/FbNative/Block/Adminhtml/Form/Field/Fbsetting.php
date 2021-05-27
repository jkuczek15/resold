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

class Fbsetting extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @var
     */
    protected $fbAttr;

    protected $_magentoAttr;


    /**
     * Retrieve group column renderer
     *
     * @return shipping
     */
    protected function _getFbAttributeRenderer()
    {
        if (!$this->fbAttr) {
            $this->fbAttr = $this->getLayout()->createBlock(
                'Ced\FbNative\Block\Adminhtml\Form\Field\FbAttribute',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->fbAttr->setClass('shipping_method_select');
        }
        return $this->fbAttr;
    }

    /**
     * Retrieve group column renderer
     *
     * @return shipping
     */
    protected function _getMagentoAttributeCodeRenderer()
    {
        if (!$this->_magentoAttr) {
            $this->_magentoAttr = $this->getLayout()->createBlock(
                'Ced\FbNative\Block\Adminhtml\Form\Field\MagentoAttributes',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_magentoAttr->setClass('shipping_method_select');
        }
        return $this->_magentoAttr;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'facebook_attribute_code',
            ['label' => __('Facebook Attribute Code'), 'renderer' => $this->_getFbAttributeRenderer()]
        );

        $this->addColumn(
            'magento_attribute_code',
            ['label' => __('Magento Attribute Code'), 'renderer' => $this->_getMagentoAttributeCodeRenderer()]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];

        $optionExtraAttr['option_' . $this->_getFbAttributeRenderer()->calcOptionHash($row->getData('facebook_attribute_code'))] =
            'selected="selected"';
        $optionExtraAttr['option_' . $this->_getMagentoAttributeCodeRenderer()->calcOptionHash($row->getData('magento_attribute_code'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );


    }
}
