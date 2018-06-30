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

namespace Ced\CsMarketplace\Block\Adminhtml\Vorders;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \Ced\CsMarketplace\Model\Vorders
     */
    protected $_vordersFactory;
    protected $_resource;
    protected $_invoice;
    protected $_vorders;
    protected $_objectManager;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Ced\CsMarketplace\Helper\Data $helperData
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Ced\CsMarketplace\Model\Vorders $vorders
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
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
    ) {
        $this->_vordersFactory = $vordersFactory;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resource;
        $this->_invoice = $invoice;
        $this->_vorders = $vorders;
        $this->_objectManager = $objectManager;
        $this->_csMarketplaceHelper = $helperData;

        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
	
	$vendor_id = $this->getRequest()->getParam('vendor_id',0);
        $sales_order_grid = $this->_resource->getTableName('sales_order_grid');

        $collection = $this->_vordersFactory->create()->getCollection();
        if($vendor_id) {
                $collection->addFieldToFilter('vendor_id', $vendor_id);
            }
        $main_table= $this->_csMarketplaceHelper->getTableKey('main_table');
        $order_total= $this->_csMarketplaceHelper->getTableKey('order_total');
        $shop_commission_fee=$this->_csMarketplaceHelper->getTableKey('shop_commission_fee');
        $collection->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")));
        
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
      
       $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'type' => 'date',
                'index' => 'created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
    $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'type' => 'text',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer'=>'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Orderid',

            ]
        );


        $this->addColumn(
            'vendor_id',
            [
                'header' => __('Vendor Name'),
                'type' => 'text',
                'index' => 'vendor_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer'=>'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname',
                'filter_condition_callback' => [$this, '_vendornameFilter'],

            ]
        );

        $this->addColumn(
            'base_order_total',
            [
                'header' => __('G.T. (Base)'),
                'type' => 'currency',
                'index' => 'base_order_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'

            ]
        );

        $this->addColumn(
            'order_total',
            [
                'header' => __('G.T.'),
                'type' => 'currency',
                'index' => 'order_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
     $this->addColumn(
            'shop_commission_fee',
            [
                'header' => __('Commission Fee'),
                'type' => 'currency',
                'index' => 'shop_commission_fee',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'net_vendor_earn',
            [
                'header' => __('Vendor Payment'),
                'type' => 'currency',
                'index' => 'net_vendor_earn',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'order_payment_state',
            [
                'header' => __('Order Payment State'),
                'index' => 'order_payment_state',
                'type' => 'options',
                'options' => $this->_invoice->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
         $this->addColumn(
            'payment_state',
            [
                'header' => __('Order State'),
                'type' => 'options',
                'options' => $this->_vorders->getStates(),
                'index' => 'payment_state',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer'=>'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Paynow',

            ]
        );


        /*$this->addColumn(
            'payment_state',
            [
                'header' => __('Vendor Payment State'),
                'index' => 'payment_state',
             //  'type' => 'options',
               // 'options' => $this->_vorders->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'renderer'=>'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Paynow',

            ]
        );*/

 
 /*
        $this->addColumn(
            'edit',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => 'view'
                        ],
                        'field' => 'vorder_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );*/
     
        return parent::_prepareColumns();
    }
   
 
    /**
     * After load collection
     *
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param $collection
     * @param $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */

     protected function _vendornameFilter($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $vendorIds =   $this->_objectManager->get('Ced\CsMarketplace\Model\vendor')->getCollection()
        ->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))
        ->getAllIds();
 
        if(count($vendorIds)>0)
            $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
        else{
            $this->getCollection()->addFieldToFilter('vendor_id');
        }   
            return $this;
    }
  
}
