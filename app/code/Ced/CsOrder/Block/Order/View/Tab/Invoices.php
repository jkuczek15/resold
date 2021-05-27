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

namespace Ced\CsOrder\Block\Order\View\Tab;
use Magento\Customer\Model\Session;

/**
 * Order Shipments grid
 */
class Invoices extends \Magento\Backend\Block\Widget\Grid\Extended  implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $_invoiceFactory;
    protected $_invoice;

    /**
     * Invoices constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ced\CsOrder\Model\ResourceModel\Invoice\CollectionFactory $invoiceFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\CsOrder\Model\ResourceModel\Invoice\CollectionFactory $invoiceFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Invoice $invoice,
        Session $customerSession,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_invoiceFactory = $invoiceFactory;
        $this->_objectManager = $objectManager;
        $this->_invoice = $invoice;
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
        $this->setId('order_invoices');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('filter'); 
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {      
        $collection = $this->_invoiceFactory->create();
        $invoiceGridTable = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('sales_invoice_grid');
        $collection->getSelect()->join(array('invoice_flat'=> $invoiceGridTable), 'invoice_flat.entity_id = main_table.invoice_id', array('invoice_flat.*', 'main_table.vendor_id'))->where("vendor_id = ".$this->_session->getVendorId()." and invoice_flat.order_id = ".$this->getOrder()->getId());
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
            'increment_id',
            [
                'header' => __('Invoice #'),
                'type' => 'text',
                'index' => 'increment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'billing_name',
            [
                'header' => __('Bill to Name'),
                'type' => 'text',
                'index' => 'billing_name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );


        $this->addColumn(
            'created_at',
            [
                'header' => __('Invoice Date'),
                'type' => 'datetime',
                'index' => 'created_at',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',

            ]
        );

        $this->addColumn(
            'state',
            [
                'header' => __('Status'),
                'type' => 'options',
                'options' => $this->_invoice->getStates(),
                'index' => 'state',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'

            ]
        );
        
        
        $this->addColumn(
            'grand_total',
            [
                'header' => __('Amount'),
                'type' => 'currency',
                'index' => 'grand_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'renderer' =>  'Ced\CsOrder\Block\Order\Invoice\Renderer\Grandtotal'

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
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject      $column
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
        return $this->getUrl('*/*/invoices', ['_current' => true,'_secure'=>true]);
    }
 
 
    
    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
   
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/invoice/view',
            array(
                'invoice_id'=> $row->getEntityId(),
                'order_id'  => $this->getRequest()->getParam('order_id'),
            '_secure'=>true
            )
        );
    }

    protected function _vendornameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $vendorIds =   $this->_objectManager->get('Ced\CsMarketplace\Model\vendor')->getCollection()
            ->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))
            ->getAllIds();
 
        if(count($vendorIds)>0) {
            $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds)); 
        }
        else{
            $this->getCollection()->addFieldToFilter('vendor_id');
        }   
           return $this;
    }
    
     /**
     * Prepare grid filter buttons
     *
     * @return void
     */
    protected function _prepareFilterButtons()
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
    }
    


    /**
     * Retrieve order model instance
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVorder()
    {
        return $this->_coreRegistry->registry('current_vorder');
    }
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Invoices');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Invoices');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
