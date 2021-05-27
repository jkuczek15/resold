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

namespace Plumrocket\GDPR\Controller\Adminhtml\Revision\History;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Plumrocket_GDPR::prgdpr';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $collectionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Request params initialization
     *
     * @return $this
     */
    private function initCurrentRequestParams()
    {
        $revisionId = $this->getRequest()->getParam('revision_id');

        if (! $revisionId) {
            if ($pageId = (int)$this->getRequest()->getParam('page_id')) {
                /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $collection */
                $collection = $this->collectionFactory->create();
                /** @var \Plumrocket\GDPR\Model\Revision $revision */
                $revision = $collection->getRevisionByPageId($pageId);
                $revisionId = $revision->getId();
            } else {
                $revisionId = 0;
            }
        }

        $this->coreRegistry->register('current_revision_id', (int)$revisionId);

        return $this;
    }

    /**
     * Retrieve transaction grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentRequestParams();
        $resultLayout = $this->resultLayoutFactory->create();

        return $resultLayout;
    }
}
