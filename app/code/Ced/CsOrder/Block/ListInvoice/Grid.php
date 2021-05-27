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

namespace Ced\CsOrder\Block\ListInvoice;
use Magento\Customer\Model\Session;
use Magento\Sales\Api\InvoiceRepositoryInterface;

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
    protected $_invoiceRepository;

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
     * @param InvoiceRepositoryInterface $invoiceRepository
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
        InvoiceRepositoryInterface $invoiceRepository,
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
        $this->_invoiceRepository =$invoiceRepository;
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
        $this->setDefaultSort('created_at');
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
        $vcollection=$this->_objectManager->get('Ced\CsOrder\Model\Invoice')->getCollection()->addFieldToFilter('vendor_id', $vendor_id);
        $vendorCreditmemoarray = array_column($vcollection->getData(), 'invoice_id');
        $collection=$this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->getCollection()->addAttributeToSelect('*');
        $coreResource   = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $salesorderGridTable = $coreResource->getTableName('sales_order_grid');
        $collection->getSelect()->joinLeft(array('order_item'=> $salesorderGridTable), 'order_item.entity_id = main_table.order_id', array('main_table.increment_id'=>'main_table.increment_id','billing_name'=>'billing_name','order_item.increment_id'=>'order_item.increment_id','order_item.created_at'=>'order_item.created_at', 'main_table.created_at'=>'main_table.created_at'));
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
             'real_order_id',
             [
                'header' => __('Invoice #'),
                'type' => 'text',
                'index' => 'main_table.increment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
             ]
         );
    
        
         $this->addColumn(
             'created_at',
             [
                'header' => __('Invoiced On'),
                'type' => 'date',
                'index' => 'main_table.created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

             ]
         );
        
          $this->addColumn(
              'sales_order_id',
              [
                'header' => __('Order #'),
                'type' => 'text',
                'index' => 'order_item.increment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

              ]
          );
           $this->addColumn(
               'order_date',
               [
                'header' => __('Order Date'),
                'type' => 'date',
                'index' => 'order_item.created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

               ]
           );

           $this->addColumn(
               'billing_name',
               [
                'header' => __('Billing To Name'),
                'type' => 'text',
                'index' => 'billing_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

               ]
           );

           $this->addColumn(
               'order_total',
               [
                'header' => __('G.T.'),
                'type' => 'number',
                'currency'=>'base_currency_code',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable'  => false,
               
                'renderer'=>'Ced\CsOrder\Block\Order\Invoice\Renderer\Grandtotal'
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

           $this->addColumn(
               'state',
               [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'state',
                'options'=>$this->getStatus(),
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
               ]
           );

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
                            'base' => '*/*/view'
                        ],
                        'field' => 'invoice_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
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
            ['invoice_id' => $row->getEntityId()]
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
    /**
     * @return array
     */
    public function getStatus()
    {
        $options=array();
        foreach ($this->_invoiceRepository->create()->getStates() as $id => $state) {
                $options[$id] = $state->render();
        }
        return $options;    
    } 
}
