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

namespace Plumrocket\GDPR\Model\ResourceModel\ExportLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Export Log collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = 'log_id';

    /**
     * Resource initialization.
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(\Plumrocket\GDPR\Model\ExportLog::class, \Plumrocket\GDPR\Model\ResourceModel\ExportLog::class);
    }
}
