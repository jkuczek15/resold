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
namespace Ced\CsMarketplace\Block\Adminhtml\Vproducts\Renderer;
class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{ 

public function render(\Magento\Framework\DataObject $row) {
	
       $id=$row->getId();
        
        $html='<a href="#popup" onClick="javascript:window.open(\''.$this->getUrl('catalog/product/edit/id/'.$id).'\')" title="'.__("Click to View").'">'.__("View").'</a>';
        return $html;
    }

}
