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

namespace Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\Grid;

use \Plumrocket\GDPR\Model\ResourceModel\ConsentsLog;

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
        $mainTable = ConsentsLog::MAIN_TABLE_NAME,
        $resourceModel = ConsentsLog::class
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
        $this->addCustomData();
    }

    /**
     * Add custom data to collection
     *
     * @return $this
     */
    public function addCustomData()
    {
        $this->getSelect()
            ->joinLeft(
                ['cg' => $this->getTable('customer_grid_flat')],
                'cg.entity_id = main_table.customer_id',
                ['cg.name as customer_name', 'cg.entity_id as customer_exist']
            )->joinLeft(
                ['sw' => $this->getTable('store_website')],
                'sw.website_id = main_table.website_id',
                ['sw.name as website']
            )->joinLeft(
                ['cp' => $this->getTable('cms_page')],
                'cp.page_id = main_table.cms_page_id',
                ['cp.title as cms_page']
            );

        $filtersMap = [
            'consent_id' => 'main_table.consent_id',
            'customer_id' => 'main_table.customer_id',
            'customer_name' => 'cg.name',
            'customer_exist' => 'cg.entity_id',
            'website' => 'sw.name',
            'created_at' => 'main_table.created_at',
            'customer_ip' => 'main_table.customer_ip',
            'location' => 'main_table.location',
            'label' => 'main_table.label',
            'cms_page_id' => 'main_table.cms_page_id',
            'cms_page' => 'cp.title',
            'version' => 'main_table.version',
        ];

        foreach ($filtersMap as $filter => $alias) {
            $this->addFilterToMap($filter, $alias);
        }

        return $this;
    }
}
