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

namespace Plumrocket\GDPR\Model\Config\Source;

class Pages implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var null|array
     */
    private $options;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * Pages constructor.
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     */
    public function __construct(
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (! $this->options) {
            $this->options = ['' => __('----')];

            $pages = $this->pageCollectionFactory->create();

            foreach ($pages as $page) {
                if ($page->getIdentifier() == \Magento\Cms\Model\Page::NOROUTE_PAGE_ID) {
                    continue;
                }

                $this->options[$page->getId()] = "[".$page->getId()."] ".$page->getTitle();
            }
        }

        return $this->options;
    }
}
