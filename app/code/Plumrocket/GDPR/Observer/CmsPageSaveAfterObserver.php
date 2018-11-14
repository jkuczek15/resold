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

namespace Plumrocket\GDPR\Observer;

class CmsPageSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision
     */
    private $revisionResource;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory
     */
    private $revisionCollectionFactory;

    /**
     * @var \Plumrocket\GDPR\Model\Revision\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\History
     */
    private $historyResource;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * CmsPageSaveAfterObserver constructor.
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision $revisionResource
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory
     * @param \Plumrocket\GDPR\Model\Revision\HistoryFactory $historyFactory
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\History $historyResource
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Model\ResourceModel\Revision $revisionResource,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory,
        \Plumrocket\GDPR\Model\Revision\HistoryFactory $historyFactory,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\History $historyResource,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->dataHelper = $dataHelper;
        $this->revisionResource = $revisionResource;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->historyFactory = $historyFactory;
        $this->historyResource = $historyResource;
        $this->authSession = $authSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $observer->getData('data_object');

        if ($this->dataHelper->moduleEnabled()
            && $page
            && ($pageId = $page->getId())
            && ($postData = $page->getData('revision'))
        ) {
            /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $revisionCollection */
            $revisionCollection = $this->revisionCollectionFactory->create();
            /** @var \Plumrocket\GDPR\Model\Revision $revisionModel */
            $revisionModel = $revisionCollection->getRevisionByPageId($pageId);
            $revisionModel->addData($postData);
            $revisionModel->setData('cms_page_id', $pageId);
            $this->revisionResource->save($revisionModel);

            /* Detect version changes */
            $origVersion = $revisionModel->getOrigData('document_version');
            $currentVersion = $revisionModel->getData('document_version');
            $isVersionChanged = ! empty($origVersion)
                ? ! $this->compareVersion($origVersion, $currentVersion)
                : false;

            if ($isVersionChanged) {
                $this->addRevisionHistory($revisionModel, $page->getOrigData('content'));
            }
        }
    }

    /**
     * @param $v1
     * @param $v2
     * @return mixed
     */
    private function compareVersion($v1, $v2)
    {
        return strtolower(trim($v1)) == strtolower(trim($v2));
    }

    /**
     * @param \Plumrocket\GDPR\Model\Revision $revision
     * @param $content
     * @return $this
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function addRevisionHistory(\Plumrocket\GDPR\Model\Revision $revision, $content)
    {
        if ($revision && $revision->getId()) {
            $documentVersion = $revision->getDocumentVersion();

            if (! empty($documentVersion)) {
                $user = $this->authSession->getUser();
                /** @var \Plumrocket\GDPR\Model\Revision\History $history */
                $history = $this->historyFactory->create();
                $history->setData([
                    'revision_id' => $revision->getId(),
                    'user_id' => $user ? $user->getId() : 0,
                    'user_name' => $user ? $user->getUserName() : 'No name',
                    'version' => $revision->getOrigData('document_version'),
                    'content' => (string)$content,
                ]);
                $this->historyResource->save($history);
            }
        }

        return $this;
    }
}
