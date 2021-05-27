<?php
/**
 * Adminhtml block for fieldset of configurable product
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\CsProduct\Block\ConfigurableProduct\Product\Steps;

class Summary extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Steps\Summary
{
	
    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Summary');
    }

    /**
     * Get url to upload files
     *
     * @return string
     */
    public function getImageUploadUrl()
    {
        return $this->getUrl('csproduct/product_gallery/upload');
    }
}
