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

namespace Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\Grid;

use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests;
use Plumrocket\GDPR\Helper\Data as DataHelper;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @param DataHelper $helper
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(// @codingStandardsIgnoreLine $mainTable and $resourceModel specified here
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = RemovalRequests::MAIN_TABLE_NAME,
        $resourceModel = RemovalRequests::class
    ) {
        $this->dataHelper = $dataHelper;

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
        $this->addCustomData();
    }

    /**
     * Add custom data to collection
     *
     * @return $this
     */
    public function addCustomData()
    {
        $anonymizationKey = $this->dataHelper->getAnonymizationKey();

        $customerNameColumn = 'IFNULL(cg.name, '
            . 'CONCAT("'.$anonymizationKey.'", "-", main_table.customer_id))'
            . ' as customer_name';

        $customerExistColumn = 'cg.entity_id as customer_exist';

        $canceledByColumn = 'IFNULL('
            . 'CONCAT(au.firstname, " ", au.lastname, " [Id:", au.user_id, "]"),'
            . ' main_table.cancelled_by)'
            . ' as cancelled_by';

        $this->getSelect()
            ->joinLeft(
                ['cg' => $this->getTable('customer_grid_flat')],
                'cg.entity_id = main_table.customer_id',
                [$customerNameColumn, $customerExistColumn]
            )->joinLeft(
                ['sw' => $this->getTable('store_website')],
                'sw.website_id = main_table.website_id',
                ['sw.name as website']
            )->joinLeft(
                ['au' => $this->getTable('admin_user')],
                'au.user_id = main_table.cancelled_by',
                [$canceledByColumn]
            );

        $filtersMap = [
            'request_id' => 'main_table.request_id',
            'customer_id' => 'main_table.customer_id',
            'customer_name' => 'cg.name',
            'customer_exist' => 'cg.entity_id',
            'website' => 'sw.name',
            'created_at' => 'main_table.created_at',
            'customer_ip' => 'main_table.customer_ip',
            'cancelled_at' => 'main_table.cancelled_at',
            'cancelled_by' => 'main_table.cancelled_by',
            'scheduled_at' => 'main_table.scheduled_at',
            'status' => 'main_table.status',
        ];

        foreach ($filtersMap as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }
}
