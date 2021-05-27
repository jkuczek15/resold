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

namespace Plumrocket\GDPR\Cron;

use Exception;
use Plumrocket\GDPR\Helper\Data;
use Plumrocket\GDPR\Helper\CustomerData;
use Plumrocket\GDPR\Model\Account\Processor;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory as RemovalResourceFactory;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Scheduler to clean accounts marked to be deleted or anonymized.
 */
class AccountRemover
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RemovalResourceFactory
     */
    protected $removalResourceFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * AccountRemover constructor.
     *
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param RemovalResourceFactory $removalResourceFactory
     * @param CustomerData $customerData
     * @param Processor $processor
     * @param Registry $registry
     * @param Data $helper
     * @param DateTime $dateTime
     */
    public function __construct(
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        RemovalResourceFactory $removalResourceFactory,
        CustomerData $customerData,
        Processor $processor,
        Registry $registry,
        Data $helper,
        DateTime $dateTime
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->customerData = $customerData;
        $this->processor = $processor;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
    }

    /**
     * Check for accounts which need to be deleted and delete them.
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->helper->moduleEnabled() || !$this->helper->isAccountDeletionEnabled()) {
            return;
        }

        $removalRequests = $this->collectionFactory
            ->create()
            ->addFieldToFilter('scheduled_at', ['lteq' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp())])
            ->addFieldToFilter('status', ['eq' => RemovalStatus::PENDING]);

        if (!$removalRequests->getItems()) {
            return;
        }

        $isSecureArea = $this->registry->registry('isSecureArea');
        if (null !== $isSecureArea) {
            $this->registry->unregister('isSecureArea');
        }
        $this->registry->register('isSecureArea', true);
        
        foreach ($removalRequests->getItems() as $removalRequest) {
            try {
                $customerId = $removalRequest->getData('customer_id');

                if ($this->customerData->hasOpenedOrders($customerId)) {
                    $this->logger->error(__("This customer [%1] has opened orders.", $customerId));
                } else {
                    $currentCustomerDataObject = $this->customerData->getCustomerDataObject($customerId);
                    $this->processor->deleteData($currentCustomerDataObject);
                    $removalRequest->addData([
                        'customer_ip' => $this->customerData->getAnonymousString($customerId),
                        'status' => RemovalStatus::COMPLETED
                    ]);
                    $this->removalResourceFactory->create()->save($removalRequest);
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $this->registry->unregister('isSecureArea');
        if (null !== $isSecureArea) {
            $this->registry->register('isSecureArea', $isSecureArea);
        }
    }
}
