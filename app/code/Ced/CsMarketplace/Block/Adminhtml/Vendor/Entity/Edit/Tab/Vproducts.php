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
 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab;
 
class Vproducts extends \Ced\CsMarketplace\Block\Adminhtml\Vproducts\Grid
{
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
	)
    {
	  parent::__construct($context, $backendHelper, $vproductsFactory, $vproducts, $objectManager, $moduleManager, $pageLayoutBuilder,$data);
	  $vendor_id = $this->getRequest()->getParam('vendor_id',0);
	  $this->setId('vproductGrids_'.$vendor_id);
	  $this->setDefaultSort('entity_id');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
    }
	
	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		$this->removeColumn('vendor_id');
		$this->removeColumn('entity_id');
		$this->removeColumn('set_name');
		return $this;
    }
	
	public function getGridUrl() {
        return $this->getUrl('*/*/vproductsgrid', array('_secure'=>true, '_current'=>true));
    }
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
		$this->getMassactionBlock()->setFormFieldName('entity_id');
	  
		$this->getMassactionBlock()->addItem('delete', array(
				'label'    => __('Delete'),
				'url'      => $this->getUrl('*/vproducts/massDelete'),
				'confirm'  => __('Are you sure?')
		));
	  
		$statuses = $this->_vproducts->getMassActionArray();
		
		$this->getMassactionBlock()->addItem('status', array(
				 'label'=> __('Change status'),
				 'url'  => $this->getUrl('*/vproducts/massStatus/', array('_current'=>true)),
				 'additional' => array(
						 'visibility' => array(
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => __('Status'),
								'default'=>'-1',
								'values' =>$statuses,
						)
				)
		));
		return $this;
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
