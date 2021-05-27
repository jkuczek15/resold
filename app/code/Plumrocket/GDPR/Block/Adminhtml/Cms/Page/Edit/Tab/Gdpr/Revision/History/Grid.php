<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr\Revision\History;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory
     */
    private $revisionCollectionFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    private $frameworkCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\Data\CollectionFactory $frameworkCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Data\CollectionFactory $frameworkCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->frameworkCollectionFactory = $frameworkCollectionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        parent::_construct();
        $this->setId('prgdpr_revision_history_grid');
        $this->setNameInLayout('prgdpr_revision_history_grid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setEmptyText(__('No Revisions Found'));
    }

    /**
     * @return int|mixed
     */
    public function getCurrentRevisionId()
    {
        $revisionId = $this->coreRegistry->registry('current_revision_id');

        if (! $revisionId) {
            if ($pageId = (int)$this->getRequest()->getParam('page_id')) {
                /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $collection */
                $collection = $this->revisionCollectionFactory->create();
                /** @var \Plumrocket\GDPR\Model\Revision $revision */
                $revision = $collection->getRevisionByPageId($pageId);
                $revisionId = $revision->getId();
            } else {
                $revisionId = 0;
            }
        }

        return $revisionId;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('prgdpr/revision_history/index', [
            'revision_id' => $this->getCurrentRevisionId(),
            '_current' => true
        ]);
    }

    /**
     * @return void
     */
    protected function _prepareCollection()// @codingStandardsIgnoreLine we need to extend parent method
    {
        /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\History\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->addFieldToFilter('revision_id', (int)$this->getCurrentRevisionId());
        $this->setCollection($collection);

        parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->addColumn(
            'version',
            [
                'header' => __('Version'),
                'align' => 'left',
                'index' => 'version',
                'type' => 'text',
                'width' => 20
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Date Last Modified'),
                'index' => 'updated_at',
                'type' => 'datetime',
                'align' => 'center',
                'width' => 260
            ]
        );

        $this->addColumn(
            'user_name',
            [
                'header' => __('Updated By'),
                'align' => 'left',
                'type' => 'text',
                'index' => 'user_name',
                'width' => 300,
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'filter' => false,
                'align' => 'left',
                'width' => 100,
                'frame_callback' => [$this, 'decorateAction'],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Remove link from rows
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return bool|string
     */
    public function getRowUrl($item)
    {
        return false;
    }

    /**
     * Decorate column action
     * $value, $row, $column, $isExport must be specified for decorate method
     *
     * @param string $value
     * @param \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function decorateAction($value, $row, $column, $isExport)// @codingStandardsIgnoreLine see docs
    {
        return sprintf(
            '<a href="javascript:void(0)" onclick="window.revisionHistoryModalManager.showModal(\'%s\')">%s</a>',
            (int)$row->getId(),
            __('View Revision')
        );
    }
}
