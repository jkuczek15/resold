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

namespace Plumrocket\GDPR\Model\ResourceModel\ExportLog\Grid;

use \Plumrocket\GDPR\Model\ResourceModel\ExportLog;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(// @codingStandardsIgnoreLine $mainTable and $resourceModel specified here
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = ExportLog::MAIN_TABLE_NAME,
        $resourceModel = ExportLog::class
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _initSelect()// @codingStandardsIgnoreLine we need to extend parent method
    {
        parent::_initSelect();
        $this->addCustomerData();
    }

    /**
     * Add customer data to collection
     *
     * @return $this
     */
    public function addCustomerData()
    {
        $this->getSelect()->joinLeft(
            ['cg' => $this->getTable('customer_grid_flat')],
            'cg.entity_id = main_table.customer_id',
            ['cg.name as customer_name', 'cg.entity_id as customer_exist']
        );

        $filtersMap = [
            'log_id' => 'main_table.log_id',
            'customer_id' => 'main_table.customer_id',
            'customer_name' => 'cg.name',
            'customer_exist' => 'cg.entity_id',
            'created_at' => 'main_table.created_at',
            'customer_ip' => 'main_table.customer_ip',
        ];

        foreach ($filtersMap as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }
}
