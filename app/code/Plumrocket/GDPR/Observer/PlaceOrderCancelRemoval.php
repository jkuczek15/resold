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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory;
use Magento\Framework\Message\ManagerInterface;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory as RemovalResourceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;
use Plumrocket\GDPR\Helper\Data as DataHelper;

/**
 * Class PlaceOrderCancelRemoval
 * @package Plumrocket\GDPR\Observer
 */
class PlaceOrderCancelRemoval implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RemovalResourceFactory
     */
    private $removalResourceFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param DataHelper $dataHelper
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param RemovalResourceFactory $removalResourceFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        DataHelper $dataHelper,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        RemovalResourceFactory $removalResourceFactory,
        DateTime $dateTime
    ) {
        $this->dataHelper = $dataHelper;
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (! $this->dataHelper->moduleEnabled()) {
            return;
        }

        $customerId = $observer->getOrder()->getCustomerId();

        $removalRequests = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->addFieldToFilter('status', ['eq' =>  RemovalStatus::PENDING]);

        if (! $removalRequests->getItems()) {
            return;
        }

        foreach ($removalRequests->getItems() as $removalRequest) {
            $removalRequest->addData([
                'cancelled_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                'cancelled_by' => 'Customer',
                'scheduled_at' => null,
                'status' => RemovalStatus::CANCELLED
            ]);
            $this->removalResourceFactory->create()->save($removalRequest);
        }

        $this->messageManager->addSuccessMessage(__("Congratulations! You have reactivated your account."));
    }
}
