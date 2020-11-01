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

 
namespace Ced\CsMarketplace\Block\Adminhtml\System\Config\Frontend\Vproducts;
 
class Category extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Return element html
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		return $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\System\Config\Frontend\Vproducts\Categories', 'csmarketplace_system_config_categories')->setElement($element)->toHtml();
	}
	   
}
