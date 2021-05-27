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

namespace Ced\CsVendorReview\Block\Adminhtml\Review;

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
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ced\CsVendorReview\Model\ResourceModel\Review\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    )
    {

        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        $this->messageManager = $messageManager;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('id');
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
            'vendor_name',
            [
                'header' => __('Vendor Name'),
                'index' => 'vendor_name',
                'class' => 'vendor_name'
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_name',
                'class' => 'customer_name'
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => __('Review Subject'),
                'index' => 'subject',
                'class' => 'subject'
            ]
        );
        $this->addColumn(
            'review',
            [
                'header' => __('Review Description'),
                'index' => 'review',
                'class' => 'review'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('status'),
                'index' => 'status',
                'class' => 'status',
                'type' => 'options',
                'options' => $this->getStatusOption(),
            ]
        );


        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    public function getStatusOption()
    {
        return [
            '0' => __('Pending'),
            '1' => __('Approved'),
            '2' => __('Disapproved')
        ];
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
