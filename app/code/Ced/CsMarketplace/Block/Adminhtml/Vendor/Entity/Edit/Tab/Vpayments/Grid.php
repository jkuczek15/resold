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
 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vpayments;
 
class Grid extends \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid
{
	/*public function _construct()
    {
	  parent::_construct();
	  $vendor_id = $this->getRequest()->getParam('vendor_id',0);
	  $this->setId('vpaymentGrids_'.$vendor_id);
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setUseAjax(true);
	  $this->setSaveParametersInSession(true);
    }*/
	
	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		$this->removeColumn('vendor_id');
		$this->getColumn('action')->setActions(array(
													array(
															'caption'   => __('View'),
															'url'       => array('base'=> '*/adminhtml_vpayments/details'),
															'onClick'   => "javascript:openMyPopup(this.href); return false;",
															'field'     => 'id'
													)
											));
		return $this;
	}
	
	public function getGridUrl() {
        return $this->getUrl('*/*/vpaymentsgrid', array('_secure'=>true, '_current'=>true));
    }
	
	/**
	 * Remove existing column
	 *
	 * @param string $columnId
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	public function removeColumn($columnId)
	{
		if (isset($this->_columns[$columnId])) {
			unset($this->_columns[$columnId]);
			if ($this->_lastColumnId == $columnId) {
				$this->_lastColumnId = key($this->_columns);
			}
		}
		return $this;
	}
}