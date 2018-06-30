<?php
/**
 * Adminhtml block for fieldset of configurable product
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\CsProduct\Block\ConfigurableProduct\Product\Steps;

class AttributeValues extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Steps\AttributeValues
{
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Attribute Values');
    }
}
