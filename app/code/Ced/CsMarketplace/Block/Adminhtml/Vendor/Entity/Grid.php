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

namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \\Ced\CsMarketplace\Model\VendorFactory
     */
    protected $_vendorFactory;
	
	protected $_websiteFactory;
    /**
     * @var \SR\Weblog\Model\Status
     */
    protected $_group;
	
	protected $_status;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
		\Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\Module\Manager $moduleManager,
		\Ced\CsMarketplace\Model\System\Config\Source\Group $group,
		\Ced\CsMarketplace\Model\System\Config\Source\Status $status,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ){
        $this->_vendorFactory = $vendorFactory;
		$this->_websiteFactory = $websiteFactory;
        $this->_group = $group;
		$this->_status = $status;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('vendor_filter');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$collection = $this->_vendorFactory->create()->getCollection()
							->addAttributeToSelect('name')
							->addAttributeToSelect('email')
							->addAttributeToSelect('group')
							->addAttributeToSelect('status');
		
		$this->setCollection($collection);
        return  parent::_prepareCollection();
    }
 
	protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareColumns()
    {
		$this->addColumn('created_at', [
				'header'    => __('Created At'),
				'align'     =>'right',
				'index'     => 'created_at',
				'type'	  => 'date'
			]
		);
		
		$this->addColumn('name', [
				'header'        => __('Vendor Name'),
				'align'         => 'left',
				'type'          => 'text',
				'index'         => 'name',
				'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
			]
		);
		
        $this->addColumn('email', [
				'header'    => __('Vendor Email'),
				'align'     =>'left',
				'index'     => 'email',
				'header_css_class' => 'col-email',
                'column_css_class' => 'col-email'
			]
		);
		
       $this->addColumn('group', [
				'header'        => __('Vendor Group'),
				'align'     	=> 'left',
				'index'         => 'group',
				'type'          => 'options',
				'options'		=> $this->_group->toFilterOptionArray(),
				'header_css_class' => 'col-group',
                'column_css_class' => 'col-group'
			]
        ); 
		
		if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'website_id',[
                    'header' => __('Websites'),
                    'index' => 'website_id',
                    'type' => 'options',
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }
				
		$this->addColumn('status', [
				'header'        => __('Vendor Status'),
				'align'     	=> 'left',
				'index'         => 'status',
				'type'          => 'options',
				'options'		=> $this->_status->toOptionArray(true),
				'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
			]
        );
		
		$this->addColumn('approve', [
				'header'        => __('Approve'),
				'align'     	=> 'left',
				'index'         => 'entity_id',
				'filter'   	 	=> false,
				'type'          => 'text',
				'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer\Approve'
			]
		);
		
		$this->addColumn('shop_disable', [
				'header'        => __('Vendor Shop Status'),
				'align'     	=> 'left',
				'index'         => 'shop_disable',
				'filter'   	 	=> false,
				'type'          => 'text',
				'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer\Disableshop'
			]
		);
		
        $this->addColumn('edit', [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit','params' => ['store' => $this->getRequest()->getParam('store')]],
                        'field' => 'vendor_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
 
    /**
     * After load collection
     *
     * @return void
     */
   /*  protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    } */  
	// commented by ankur to avoid the error on the vendor grid afterload error

    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
	/* protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())){
            return ;
        }
        $this->getCollection()->addStoreFilter($value);
    } */
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('vendor_id');
 
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete Vendor(s)'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
 
        $statuses = $this->_status->toOptionArray();
		
		$this->getMassactionBlock()->addItem('status',
				[
				'label'=> __('Change Vendor(s) Status'),
				'url'  => $this->getUrl('*/*/massStatus/', ['_current'=>true]),
				'additional' => [
						'visibility' => [
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => ('Status'),
								'default'=>'-1',
								'values' =>$statuses,
						]
				]
				]
		);
		
		$this->getMassactionBlock()->addItem('shop_disable',
			[
  			'label'=>__('Change Vendor Shops'),
  			'url'  => $this->getUrl('*/*/massDisable/', ['_current'=>true]),
  			'additional' => [
  					'visibility' => [
  							'name' => 'shop_disable',
  							'type' => 'select',
  							'class' => 'required-entry',
  							'label' => __('Vendor Shop Status'),
  							'default'=>'-1',
  							'values' =>[
										['value' => \Ced\CsMarketplace\Model\Vshop::ENABLED, 'label'=>__('Enabled')],
										['value' => \Ced\CsMarketplace\Model\Vshop::DISABLED, 'label'=>__('Disabled')]
									],
					]
				]
			]
		);
				
        return $this;
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
        return $this->getUrl('*/*/edit',
            ['vendor_id' => $row->getId()]
        );
    }
}
