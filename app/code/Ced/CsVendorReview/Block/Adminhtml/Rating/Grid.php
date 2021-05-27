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
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsVendorReview\Block\Adminhtml\Rating;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $moduleManager;

    protected $_setsFactory;

    protected $_productFactory;


    protected $_type;


    protected $_status;
    protected $_collectionFactory;


    protected $_visibility;


    protected $_websiteFactory;
    public $messageManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Ced\CsVendorReview\Model\ResourceModel\Rating\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    )
    {

        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->messageManager = $messageManager;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        try {
            $collection = $this->_collectionFactory->load();
            $this->setCollection($collection);

            parent::_prepareCollection();

            return $this;
        } catch (\Exception $e) {
            $this->messageManager->addWarning(__($e->getMessage()));
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'index' => 'id',
                'class' => 'id'
            ]
        );
        $this->addColumn(
            'rating_label',
            [
                'header' => __('Rating Label'),
                'index' => 'rating_label',
                'class' => 'rating_label'
            ]
        );
        $this->addColumn(
            'rating_code',
            [
                'header' => __('Rating Code'),
                'index' => 'rating_code',
                'class' => 'rating_code'
            ]
        );
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order',
                'class' => 'sort_order'
            ]
        );


        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('csvendorreview/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('csvendorreview/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'csvendorreview/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
