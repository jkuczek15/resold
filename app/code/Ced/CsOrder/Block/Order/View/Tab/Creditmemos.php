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
class Creditmemos extends \Magento\Backend\Block\Widget\Grid\Extended  implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $_creditMemoFactory;
    protected $_creditmemo;

    /**
     * Creditmemos constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ced\CsOrder\Model\ResourceModel\Creditmemo\CollectionFactory $creditMemoFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Ced\CsOrder\Model\ResourceModel\Creditmemo\CollectionFactory $creditMemoFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        Session $customerSession,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_creditMemoFactory = $creditMemoFactory;
        $this->_objectManager = $objectManager;
        $this->_creditmemo = $creditmemo;
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
        $this->setId('order_creditmemos');
        $this->setDefaultSort('id');
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
        $collection = $this->_creditMemoFactory->create();
        $invoiceGridTable = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('sales_creditmemo_grid');
        $collection->getSelect()->join(array('creditmemo_flat'=> $invoiceGridTable), 'creditmemo_flat.entity_id = main_table.creditmemo_id', array('creditmemo_flat.*' , 'main_table.vendor_id'))->where("vendor_id = ".$this->_session->getVendorId()." and creditmemo_flat.order_id = ".$this->getOrder()->getId());
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
                'header' => __('Credit Memo #'),
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
                'header' => __('Created At'),
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
                'options' => $this->_creditmemo->getStates(),
                'index' => 'state',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'

            ]
        );

        if($this->getVorder()->isAdvanceOrder()) {
            $this->addColumn(
                'base_grand_total',
                [
                'header' => __('Amount'),
                'type' => 'currency',
                'index' => 'base_grand_total',
                'renderer'  => 'Ced\CsOrder\Block\Order\Creditmemo\Renderer\Grandtotal'    
                ]
            );
        }else{
            $this->addColumn(
                'base_grand_total',
                [
                'header' => __('Amount'),
                'type' => 'currency',
                'index' => 'base_grand_total',
                'renderer'  => 'Ced\CsOrder\Block\Order\Creditmemo\Renderer\Grandtotal'    
                ]
            );
        }
        
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
        return $this->getUrl('*/*/creditmemos', ['_current' => true,'_secure'=>true]);
    }
 
 
    
    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
   
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/creditmemo/view',
            array(
                'creditmemo_id'=> $row->getEntityId(),
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
        return __('Credit Memos');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Credit Memos');
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
