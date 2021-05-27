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
 
namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit\Tab\Addorder\Search;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_objectManager;
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = [])
    {

    
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //parent::__construct();
   
        parent::__construct($context, $backendHelper, $data);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
        $this->setId('ced_csmarketplace_order_search_grid123');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	 public function getStore() {


		$storeId = (int) $this->getRequest()->getParam('store', 0);
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');


        if($storeId)
			return $storeManager->getStore($storeId);
		else 
			return $storeManager->getStore();
	 }


    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection()
    {
    	$flag = false;
		$params = $this->getRequest()->getParams();
		$type = isset($params['type']) && in_array($params['type'],array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
		$vendorId = isset($params['vendor_id'])? $params['vendor_id']:0;
		$orderIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
		$orderTable = $resource->getTableName('sales_order');
		$collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getCollection();

		
		$collection ->addFieldToFilter('main_table.vendor_id',array('eq'=>$vendorId));
		
		if($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
			$collection->addFieldToFilter('main_table.order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
					   ->addFieldToFilter('main_table.payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_REFUND));
		} else {
			
			$collection->addFieldToFilter('main_table.order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
					   ->addFieldToFilter('main_table.payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_OPEN));
		}
					
		$collection->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr('(main_table.order_total - main_table.shop_commission_fee)')));
		$collection->getSelect()->joinLeft($orderTable , 'main_table.order_id ='.$orderTable.'.increment_id',array('*'));
		
		
		$this->setCollection($collection);

        return $this;
    }


    /**
     * Prepare columns
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
		$this->addColumn('created_at', array(
            'header' => __('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));
  		$this->addColumn('order_id', array(
  			'header'    => __('Order ID#'),
  			'align'     => 'left',
   			'index'     => 'order_id',
  			'filter_index'  => 'order_id',
  			)); 
		
		$this->addColumn('base_order_total',array(
            'header'=> __('G.T. (Base)'),
            'index' => 'base_order_total',
			'type'          => 'currency',
             'currency' => 'base_currency_code',
		    
		    ));
		$this->addColumn('order_total',array(
            'header'=> __('G.T.'),
            'index' => 'order_total',
			'type'          => 'currency',
             'currency' =>'currency',
		    ));
		
			
		$this->addColumn('shop_commission_fee',array(
            'header'=> __('Commission Fee'),
            'index' => 'shop_commission_fee',
			'type'          => 'currency',
             'currency' =>'currency',
		    
		    ));
			
		$this->addColumn('net_vendor_earn',array(
				'header'=> __('Vendor Payment'),
				'index' => 'net_vendor_earn',
				'type'          => 'currency',
				 'currency' =>'currency',
		));
		$this->addColumnAfter('relation_id', array(
            'header'=>__('Select'),
            'sortable'=>false,
			'header_css_class' => 'a-center',
			'inline_css' => 'csmarketplace_relation_id checkbox',
            'index'=>'id',
            'type' => 'checkbox',
			'field_name' => 'in_orders',
			'values' => $this->_getSelectedOrders(),
			'disabled_values' => array(),
            'align' => 'center',
        ), 'net_vendor_earn');
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
		return $this->getUrl('*/*/loadBlock', array('block'=>'search_grid', '_current' => true, 'collapse' => null));
    }
	
    protected function _getSelectedOrders()
    {
		$params = $this->getRequest()->getParams();
		$orderIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
        return $orderIds;
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