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

namespace Plumrocket\GDPR\Helper;

use Plumrocket\GDPR\Helper\Data;
use Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory;
use Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory as RevisionCollectionFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;

class Notifys extends AbstractHelper
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepositoryInterface;

    /**
     * @var CollectionFactory
     */
    protected $consentsLogCollectionFactory;

    /**
     * @var RevisionCollectionFactory
     */
    protected $revisionCollectionFactory;

    /**
     * @var array | null
     */
    protected $notifys = null;

    /**
     * Notifys constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param Data $helper
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param CollectionFactory $consentsLogCollectionFactory
     * @param RevisionCollectionFactory $revisionCollectionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Data $helper,
        PageRepositoryInterface $pageRepositoryInterface,
        CollectionFactory $consentsLogCollectionFactory,
        RevisionCollectionFactory $revisionCollectionFactory
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->pageRepositoryInterface  = $pageRepositoryInterface;
        $this->consentsLogCollectionFactory = $consentsLogCollectionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->logger = $context->getLogger();
    }

    /**
     * @param null $excludePages
     * @param int | string $store
     * @return array
     */
    public function getNotifys(// @codingStandardsIgnoreLine $store will need in future
        $excludePages = null,
        $store = null
    ) {
        if (! $this->helper->moduleEnabled()) {
            return [];
        }

        if ($this->customerSession->getPrgdprRemindlaterNotifys()) {
            return [];
        }

        if (null === $this->notifys) {
            $this->notifys = [];
            $notifysCollection = $this->revisionCollectionFactory->create()
                ->addFieldToFilter('enable_revisions', ['eq' => 1])
                ->addFieldToFilter('notify_via_popup', ['eq' => 1]);

            if (!empty($excludePages)) {
                $notifysCollection->addFieldToFilter('cms_page_id', ['nin' => $excludePages]);
            }

            if ($notifysCollection->getSize()) {
                foreach ($notifysCollection as $item) {
                    $data = [];
                    try {
                        $data['consentId'] = $item->getRevisionId();
                        $data['notify_content'] = $item->getPopupContent();
                        $data['agreeUrl'] = $this->urlBuilder->getUrl(
                            'prgdpr/consentpopups/confirm',
                            ['_secure' => true]
                        );
                        $data['remindLaterUrl'] = $this->urlBuilder->getUrl(
                            'prgdpr/consentpopups/remindlater',
                            ['_secure' => true]
                        );
                        $data['page_id'] = $item->getCmsPageId();
                        $data['checkbox_label'] = '';
                        $data['cms_page'] = null;

                        if ($data['page_id']) {
                            $cmsPage = $this->pageRepositoryInterface->getById($data['page_id']);
                            $data['cms_page'] = [
                                'content'   => $cmsPage->getContent(),
                                'version'   => $item->getDocumentVersion()
                            ];
                            $data['checkbox_label'] = $cmsPage->getTitle();
                        }
                    } catch (\Exception $e) {
                        $data['cms_page'] = null;
                        $this->logger->error($e->getMessage());
                    }

                    if (!$this->isAlreadyChecked($data)) {
                        $this->notifys[$data['consentId']] = $data;
                    }
                }
            }
        }

        return $this->notifys;
    }

    /**
     * @param $notify
     * @return bool
     */
    public function isAlreadyChecked($notify)
    {
        $customerId = $this->customerSession->getCustomerId();

        if ($customerId) {
            $consentsLogCollection = $this->consentsLogCollectionFactory->create()
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);

            if ($notify['cms_page']) {
                $consentsLogCollection->addFieldToFilter('cms_page_id', ['eq' => $notify['page_id']]);
                $consentsLogCollection->addFieldToFilter('version', ['eq' => $notify['cms_page']['version']]);
            }

            if ($consentsLogCollection->getSize()) {
                return true;
            }
        }

        return false;
    }
}
