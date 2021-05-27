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

class ProductStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_vproduct;
	protected $_objectManager;
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Ced\CsMarketplace\Model\Vproducts $vproduct,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
    	   $this->_objectManager = $objectManager;
        $this->_vproduct = $vproduct;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
    	  $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($row->getProductId());
       $vOptionArray=$this->_vproduct->getVendorOptionArray();
        if($row->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS)
            return $vOptionArray[$row->getCheckStatus().$product->getStatus()];
        else 
            return $vOptionArray[$row->getCheckStatus()];
    }
}
