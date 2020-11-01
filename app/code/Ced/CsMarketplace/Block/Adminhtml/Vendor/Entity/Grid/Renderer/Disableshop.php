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

namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer;
 
class Disableshop extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {		 
	protected $_objectManager;
	
	protected $urlBuilder;
	 
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,\Magento\Framework\UrlInterface $urlBuilder){
		$this->_objectManager = $objectManager;
		$this->urlBuilder = $urlBuilder;
    }
	
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$html = '';
		$model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vshop')->loadByField('vendor_id',array($row->getEntityId()));
		//print_r($model->getCollection()->getData());
		
		if($model->getId()!='' && $model->getShopDisable() == \Ced\CsMarketplace\Model\Vshop::ENABLED){
			$url =  $this->urlBuilder->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>\Ced\CsMarketplace\Model\Vshop::DISABLED, 'inline'=>1));			
			$html .= __('Enabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Disable?').'\', \''. $url . '\');" >'.__('Disable').'</a>';  
		}
		else if($model->getId()!='' && $model->getShopDisable() == \Ced\CsMarketplace\Model\Vshop::DISABLED) {
			$url =  $this->urlBuilder->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>\Ced\CsMarketplace\Model\Vshop::ENABLED, 'inline'=>1));
				
			$html .= __('Disabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Enable?').'\', \''. $url . '\');" >'.__('Enable')."</a>";
		}
		else{
			$url =  $this->urlBuilder->getUrl('*/*/massDisable', array('vendor_id' => $row->getEntityId(), 'shop_disable'=>\Ced\CsMarketplace\Model\Vshop::DISABLED, 'inline'=>1));
				
			$html .= __('Enabled').'&nbsp;'.'<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Disable?').'\', \''. $url . '\');" >'.__('Disable').'</a>';
		}
		return $html;
	}
}