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

namespace Plumrocket\GDPR\Plugin\Magento\Cms\Model\Page;

class DataProviderPlugin
{
    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory
     */
    private $collectionFactory;

    /**
     * DataProviderPlugin constructor.
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $collectionFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * After get data Plugin
     *
     * Argument $subject must be specified
     * The first argument for the after methods is an object that provides access to all public methods
     * of the observed method’s class.
     * see https://devdocs.magento.com/guides/v2.0/extension-dev-guide/plugins.html
     *
     * @param \Magento\Cms\Model\Page\DataProvider $subject
     * @param $result
     * @return array
     */
    public function afterGetData(// @codingStandardsIgnoreLine see docs for details
        \Magento\Cms\Model\Page\DataProvider $subject,
        $result
    ) {
        if ($this->dataHelper->moduleEnabled()) {
            $pageIds = is_array($result) && ! empty($result) ? array_keys($result) : null;

            if (is_array($pageIds) && ! empty($pageIds)) {
                /** @var \Plumrocket\GDPR\Model\ResourceModel\Revision\Collection $collection */
                $collection = $this->collectionFactory->create();
                $collection->addFieldToFilter('cms_page_id', ['in' => $pageIds]);

                if ($collection->getSize()) {
                    /** @var \Plumrocket\GDPR\Model\Revision $item */
                    foreach ($collection->getItems() as $item) {
                        $pageId = $item->getCmsPageId();

                        if (isset($result[$pageId])) {
                            $data = $item->getData();
                            $data['original_enable_revisions'] = $item->getOrigData('enable_revisions');
                            $data['original_document_version'] = $item->getOrigData('document_version');
                            $data['original_content'] = ! empty($result[$pageId]['content'])
                                ? $result[$pageId]['content']
                                : '';
                            $result[$pageId]['revision'] = $data;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
