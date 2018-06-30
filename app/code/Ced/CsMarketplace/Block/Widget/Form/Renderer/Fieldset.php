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

 
namespace Ced\CsMarketplace\Block\Widget\Form\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
 
class Fieldset extends \Magento\Framework\View\Element\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_element;

	protected $_template = 'Ced_CsMarketplace::widget/form/renderer/fieldset.phtml';
	

    public function getElement()
    {
        return $this->_element;
    }

    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
