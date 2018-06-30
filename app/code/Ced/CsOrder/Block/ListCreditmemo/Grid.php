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
namespace Ced\CsOrder\Block\ListCreditmemo;
use Magento\Customer\Model\Session;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
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
    protected $_creditmemoRepository;

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
     * @param CreditmemoRepositoryInterface $creditmemoRepository
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
        CreditmemoRepositoryInterface $creditmemoRepository,
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
        $this->_creditmemoRepository=$creditmemoRepository;
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
     * Prepare Collection
     *
     * @return object
     */
    protected function _prepareCollection()
    {
        $vendor_id = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        $vcollection=$this->_objectManager->get('Ced\CsOrder\Model\Creditmemo')->getCollection()->addFieldToFilter('vendor_id', $vendor_id);
        $vendorCreditmemoarray=array_column($vcollection->getData(), 'creditmemo_id');
        $collection=$this->_objectManager->get('Magento\Sales\Model\Order\Creditmemo')->getCollection()->addAttributeToSelect('*');
        $coreResource   = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $salesorderGridTable = $coreResource->getTableName('sales_order_grid');
        $collection->getSelect()->joinLeft(array('order_item'=> $salesorderGridTable), 'order_item.entity_id = main_table.order_id', array('main_table.increment_id'=>'main_table.increment_id','shipping_name'=>'shipping_name','order_item.increment_id'=>'order_item.increment_id', 'order_item.created_at'=>'order_item.created_at', 'main_table.created_at'=>'main_table.created_at'));
        $collection->addFieldToFilter('main_table.entity_id', array('in'=>$vendorCreditmemoarray));
        $collection->addFilterToMap('base_grand_total','main_table.base_grand_total');
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
            'header'    => __('Creditmemo ID #'),
            'type'      => 'text',
            'index'     => 'main_table.increment_id',
            ]
        );


        $this->addColumn(
            'created_at', [
            'header'    => __('Created'),
            'index'     => 'main_table.created_at',
            'type'      => 'datetime',
            ]
        );

        $this->addColumn(
            'order_id', [
            'header'    => __('Order #'),
            'index'     => 'order_item.increment_id',
            'type'      => 'text',
            ]
        );

        $this->addColumn(
            'order_created_at', [
            'header'    => __('Order Date'),
            'index'     => 'order_item.created_at',
            'type'      => 'datetime',
            ]
        );

        $this->addColumn(
            'shipping_name', [
            'header' => __('Ship to Name'),
            'index' => 'shipping_name',
            ]
        );
        $this->addColumn(
            'base_grand_total', [
            'header' => __('Refunded'),
            'type'  =>'number',
            'currency_code'=>'base_currency_code',
            'index' => 'base_grand_total',
            'renderer'=>'Ced\CsOrder\Block\Order\Creditmemo\Renderer\Grandtotal',
             'filter_condition_callback' => array($this, '_vendorrefund')
            ]
        );
        $this->addColumn(
            'state', [
            'header' => __('Status'),
            'index' => 'state',
            'type'=>'options',
            'options'=> $this->getStatus(),
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
                        'field'   => 'creditmemo_id'
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
 
    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/view',
            ['creditmemo_id' => $row->getEntityId()]
        );
    }
    
 // Custom Refund Filter 
     protected function _vendorrefund($collection, $column)
     {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
         
        if($value['from'])
        {
          $collection->addFieldToFilter('base_grand_total', array('gteq'=>$value['from'])); 
        }

        if ($value['to']) 
        {
          $collection->addFieldToFilter('base_grand_total', array('lteq'=>$value['to']));  
        } 
        
        return $collection;
    }

    public function getStatus()
    {
        $options=array();
        foreach ($this->_creditmemoRepository->create()->getStates() as $id => $state) {
                $options[$id] = $state->render();
        }
        return $options;    
    } 
}
