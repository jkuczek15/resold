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

namespace Ced\CsOrder\Block\ListOrders;
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

        $vendor = $this->_session->getVendorId();
        $collection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vorders')->getCollection()->addFieldToFilter('vendor_id', $vendor );

        $main_table= $this->_csMarketplaceHelper->getTableKey('main_table');
        $order_total= $this->_csMarketplaceHelper->getTableKey('base_order_total');
        $shop_commission_base_fee=$this->_csMarketplaceHelper->getTableKey('shop_commission_base_fee');
        $collection->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_base_fee})")));
       
        $collection->addFilterToMap('net_vendor_earn','main_table.base_order_total');
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
                'header' => __('Order #'),
                'type' => 'text',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Purchased On'),
                'type' => 'date',
                'index' => 'created_at',
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
                'type' => 'currency',
                'index' => 'base_order_total',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'

            ]
        );

        $this->addColumn(
            'shop_commission_fee',
            [
                'header' => __('Commission Fee'),
                'type' => 'currency',
                'index' => 'shop_commission_base_fee',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'net_vendor_earn',
            [
                'header' => __('Net Earned'),
                'type' => 'currency',
                'index' => 'net_vendor_earn',
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
                'header' => __('Order Payment<br /> Status'),
                'index' => 'order_payment_state',
                'type' => 'options',
                'options' => $this->_objectManager->get('\Ced\CsOrder\Model\Invoice')->getStates(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
         $this->addColumn(
             'payment_state',
             [
                'header' => __('Vendor Payment <br /> Status'),
                'type' => 'options',
                'options' => $this->_vorders->getStates(),
                'index' => 'payment_state',
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
                        'field' => 'order_id'
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
 
    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return 'javascript:void(0);';
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
  
}
