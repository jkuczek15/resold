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
 
class Vorders extends \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid
{
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Sales\Model\Order\Invoice $invoice,
		\Ced\CsMarketplace\Helper\Data $helperData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
	)
    {
	  parent::__construct($context, $backendHelper, $vordersFactory, $moduleManager, $pageLayoutBuilder, $invoice,$helperData, $resource, $vorders, $objectManager, $data);
	  $vendor_id = $this->getRequest()->getParam('vendor_id',0);
	  $this->setId('vordersGrids_'.$vendor_id);
	  $this->setDefaultSort('created_at');
	  $this->setDefaultDir('DESC');
	  $this->setSaveParametersInSession(true);
	  $this->setUseAjax(true);
    }
	
	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		$this->removeColumn('vendor_id');
		return $this;
	}
	
	public function getGridUrl() {
        return $this->getUrl('*/*/vordersgrid', array('_secure'=>true, '_current'=>true));
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