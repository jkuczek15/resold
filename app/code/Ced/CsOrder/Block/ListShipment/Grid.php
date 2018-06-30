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
  * @category  Ced
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsOrder\Block\ListShipment;
use Magento\Customer\Model\Session;
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
    protected $_session;
    protected $_csMarketplaceHelper;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VordersFactory $vordersFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Ced\CsMarketplace\Model\Vorders $vorders
     * @param \Ced\CsMarketplace\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VordersFactory $vordersFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Ced\CsMarketplace\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $customerSession,
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
        $this->setData('area', 'adminhtml');
        $this->_session = $customerSession;
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('entity_id');
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
        $vendor_id = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        $vcollection=$this->_objectManager->get('Ced\CsOrder\Model\Shipment')->getCollection()->addFieldToFilter('vendor_id', $vendor_id);
        $vendorCreditmemoarray=array_column($vcollection->getData(), 'shipment_id');
        $collection=$this->_objectManager->get('Magento\Sales\Model\Order\Shipment')->getCollection()->addAttributeToSelect('*');
        /*$collection->getSelect()->join(array('order_item'=> 'sales_order_grid'), 'order_item.entity_id = main_table.order_id', array('main_table.increment_id'=>'main_table.increment_id','shipping_name'=>'shipping_name','order_item.increment_id'=>'order_item.increment_id'));*/
        $coreResource   = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $salesorderGridTable = $coreResource->getTableName('sales_order_grid');
        $collection->getSelect()->join(array('order_item'=> $salesorderGridTable), 'order_item.entity_id = main_table.order_id', array('main_table.increment_id'=>'main_table.increment_id','shipping_name'=>'shipping_name','order_item.increment_id'=>'order_item.increment_id', 'order_item.created_at'=>'order_item.created_at', 'main_table.created_at'=>'main_table.created_at'));
        $collection->addFieldToFilter('main_table.entity_id', array('in'=>$vendorCreditmemoarray));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id', [
            'header'    => __('Shipment #'),
            'type'      => 'text',
            'index'     => 'main_table.increment_id',
            ]
        );


        /*$this->addColumn(
            'created_at', [
            'header'    => __('Date Shipped'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            ]
        );*/

       $this->addColumn(
             'created_at',
             [
                'header' => __('Date Shipped'),
                'type' => 'date',
                'index' => 'main_table.created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

             ]
         );

        $this->addColumn(
            'order_id', [
            'header'    => __('Order #'),
            'index'     => 'order_item.increment_id',
            'type'      => 'text',
            ]
        );

        /*$this->addColumn(
            'order_created_at', [
            'header'    => __('Order Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            ]
        );*/

        $this->addColumn(
               'order_created_at',
               [
                'header' => __('Order Date'),
                'type' => 'date',
                'index' => 'order_item.created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

               ]
        );

        $this->addColumn(
            'shipping_name', [
            'header' => __('Ship to Name'),
            'index' => 'shipping_name',
            ]
        );

        $this->addColumn(
            'total_qty', [
            'header' => __('Shiping Qty'),
            'index' => 'total_qty',
            'renderer'=>'Ced\CsOrder\Block\Order\Shipment\Renderer\Totalqty'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header'    => __('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => 'View',
                        'url'     => array('base'=>'*/*/view'),
                        'field'   => 'shipment_id'
                    ]
                ],
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ]
        );
        
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
     * @param                                         \Magento\Framework\Data\Collection $collection
     * @param                                         \Magento\Framework\DataObject      $column
     * @return                                        void
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
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/view',
            ['shipment_id' => $row->getEntityId()]
        );
    }
    
     /*protected function _vendornameFilter($collection, $column){
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
    }*/
    
     /**
     * Prepare grid filter buttons
     *
     * @return void
     */
    /*protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }*/
  
}
