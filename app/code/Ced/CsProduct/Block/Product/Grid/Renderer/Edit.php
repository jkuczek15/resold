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

namespace Ced\CsProduct\Block\Product\Grid\Renderer;

class Edit extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_vproduct;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Ced\CsMarketplace\Model\Vproducts $vproduct,
    		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_vproduct = $vproduct;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
    	$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($row->getProductId());
    	$attributeSetId = $product->getAttributeSetId();
        $url =  $this->getUrl('*/*/edit', ['set'=>$attributeSetId,'id' => $row->getProductId(),'store'=> (int)$this->getRequest()->getParam('store',0)]);
        return "<a href='$url' target='_self'>".__('Edit')."</a>";
    	
    }
}
