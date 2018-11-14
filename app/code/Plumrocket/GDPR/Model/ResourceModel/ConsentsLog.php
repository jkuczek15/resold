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

namespace Plumrocket\GDPR\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Consents Log resource model.
 */
class ConsentsLog extends AbstractDb
{
    /**
     * Name of Main Table
     */
    const MAIN_TABLE_NAME = 'plumrocket_gdpr_consents_log';

    /**
     * Name of Primary Column
     */
    const ID_FIELD_NAME = 'consent_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()// @codingStandardsIgnoreLine we need to extend parent method
    {
        $this->_init(self::MAIN_TABLE_NAME, self::ID_FIELD_NAME);
    }

    /**
     * @param $data
     * @return int
     */
    public function saveMultipleConsents($data)
    {
        $table = $this->getTable(self::MAIN_TABLE_NAME);
        $connection = $this->getConnection();

        return $connection->insertMultiple($table, $data);
    }
}
