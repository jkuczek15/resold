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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Adminhtml\Vproducts;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var  \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;

    /**
     * @var  \Ced\CsMarketplace\Model\Vproducts
     */
    protected $_vproducts;

    /**
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \SR\Weblog\Model\Status
     */
    // protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SR\Weblog\Model\BlogPostsFactory $blogPostFactory
     * @param \SR\Weblog\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    )
    {
        $this->_vproductsFactory = $vproductsFactory;
        $this->_vproducts = $vproducts;
        $this->_objectManager = $objectManager;
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
        $this->setDefaultSort('entity_id');
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
	
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		$allowedIds = array();
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('usePendingProductFilter')){
    			$vproducts = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS,0,0,-1);
    			$this->_objectManager->get('Magento\Framework\Registry')->unregister('usePendingProductFilter');
    			$this->_objectManager->get('Magento\Framework\Registry')->unregister('useApprovedProductFilter');
    	} elseif($this->_objectManager->get('Magento\Framework\Registry')->registry('useApprovedProductFilter') ){
    			$vproducts = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS,0,0,-1);
    			$this->_objectManager->get('Magento\Framework\Registry')->unregister('useApprovedProductFilter');
    			$this->_objectManager->get('Magento\Framework\Registry')->unregister('usePendingProductFilter');
    	} else {
			$vproducts = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',0,0,-1);
		}
		foreach($vproducts as $vproduct) {
			$allowedIds[] = $vproduct->getProductId();
		}		
    	   
        $store = $this->_getStore();
        $collection = $this->_objectManager->get('Magento\Catalog\Model\Product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
			->addAttributeToFilter('entity_id',array('in'=>$allowedIds));

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
        }
        $collection->joinField('check_status','ced_csmarketplace_vendor_products', 'check_status','product_id=entity_id',null,'left');
        $collection->joinField('vendor_id','ced_csmarketplace_vendor_products', 'vendor_id','product_id=entity_id',null,'left');
        $collection->joinField('website_id','ced_csmarketplace_vendor_products', 'website_id','product_id=entity_id',null,'left');
        
		if($vendor_id) {
			$collection->addFieldToFilter('vendor_id',array('eq'=>$vendor_id));
		}
        $this->setCollection($collection);
		
        parent::_prepareCollection();
        
        
        $this->getCollection()->addWebsiteNamesToResult();
        
        return $this;
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeId);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn('vendor_id',
            array(
                'header' => __('Vendor Name'),
                'align' => 'left',
                'width' => '100px',
                'index' => 'vendor_id',
                'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname',
                'filter_condition_callback' => array($this, '_vendornameFilter'),
            ));


        $this->addColumn('type_id',
            array(
                'header' => __('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->_objectManager->get('Magento\Catalog\Model\Product\Type')->getOptionArray(),
            ));
        $sets = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter($this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product')->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header' => __('Attrib. Set Name'),
                'width' => '60px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
            ));

        $this->addColumn('sku',
            array(
                'header' => __('SKU'),
                'index' => 'sku',
            ));
        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header' => __('Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header' => __('Qty'),
                    'width' => '50px',
                    'type' => 'number',
                    'index' => 'qty',
                ));
        }


        if (!$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode()) {
            $this->addColumn(
                'website_id',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'website_id',
                    'type' => 'options',
                    'options' => $this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }

        if (!$this->_objectManager->get('Magento\Framework\Registry')->registry('usePendingProductFilter') && !$this->_objectManager->get('Magento\Framework\Registry')->registry('useApprovedProductFilter')) {
            $this->addColumn('check_status',
                array(
                    'header' => __('Status'),
                    'width' => '70px',
                    'index' => 'check_status',
                    'type' => 'options',
                    'options' => $this->_vproducts->getOptionArray(),
                ));
        }

        $this->addColumn('action',
            array(
                'header' => __('Action'),
                'type' => 'text',
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vproducts\Renderer\Action',
                'index' => 'action',
            ));
        $this->addColumn('view',
            array(
                'header' => __('View'),
                'type' => 'text',
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Ced\CsMarketplace\Block\Adminhtml\Vproducts\Renderer\View',
                'index' => 'view',
            ));

        return parent::_prepareColumns();
    }

    /**
     * After load collection
     * @param $collection
     * @param \Magento\Framework\DataObject $column
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _vendornameFilter($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $vendors = $this->_objectManager->get('\Ced\CsMarketplace\Model\VendorFactory')->create()->getCollection()
            ->addAttributeToFilter('name', ['like' => $value . '%']);
        $vendor_id = array();
        foreach ($vendors as $_vendor) {
            $vendor_id[] = $_vendor->getId();
        }
        $this->getCollection()->addFieldToFilter('vendor_id', array('eq' => $vendor_id));
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
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        $statuses = $this->_vproducts->getMassActionArray();

        $this->getMassactionBlock()->addItem('status', array(
            'label' => __('Change status'),
            'url' => $this->getUrl('*/*/massStatus/', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',

                    'label' => __('Status'),
                    'default' => '-1',
                    'values' => $statuses,
                )
            )
        ));
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        //$action=$this->getRequest()->getActionName();
        return $this->getUrl('*/*/vproductgrid', array('_secure' => true, '_current' => true));
    }

    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}