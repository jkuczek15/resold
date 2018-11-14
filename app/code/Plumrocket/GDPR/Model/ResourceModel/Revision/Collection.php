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

namespace Plumrocket\GDPR\Model\ResourceModel\Revision;

/**
 * Revision Collection model.
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(
            \Plumrocket\GDPR\Model\Revision::class,
            \Plumrocket\GDPR\Model\ResourceModel\Revision::class
        );
    }

    /**
     * @param $pageId
     * @return \Magento\Framework\DataObject
     */
    public function getRevisionByPageId($pageId)
    {
        return $this->addFieldToFilter('cms_page_id', (int)$pageId)
            ->setPageSize(1)
            ->getFirstItem();// @codingStandardsIgnoreLine we set limit in previous line
    }
}
