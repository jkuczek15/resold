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
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{ 
	public function render(\Magento\Framework\DataObject $row) {
        $sure="you Sure?";
        if($row->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS)
        	$html='<a href="'.$this->getUrl('csmarketplace/vproducts/changeStatus/status/0/id/' . $row->getId()).'" title="'.__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.__("Disapprove").'</a>';
        if($row->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS)
        	$html='<a href="'.$this->getUrl('csmarketplace/vproducts/changeStatus/status/1/id/' . $row->getId()).'"  title="'.__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.__("Approve").'</a>
        		 | <a href="'.$this->getUrl('csmarketplace/vproducts/changeStatus/status/0/id/' . $row->getId()).'" title="'.__("Click to Disapprove").'" onclick="return confirm(\'Are you sure, You want to disapprove?\')">'.__("Disapprove").'</a>';
        if($row->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS)
        	$html='<a href="'.$this->getUrl('csmarketplace/vproducts/changeStatus/status/1/id/' . $row->getId()).'"  title="'.__("Click to Approve").'" onclick="return confirm(\'Are you sure, You want to approve?\')">'.__("Approve").'</a>';
        return $html;
    }

}