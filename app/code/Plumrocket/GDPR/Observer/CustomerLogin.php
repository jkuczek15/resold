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
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\Config\ConfigInterface;
use Plumrocket\GDPR\Model\RemovalRequests;
use Plumrocket\GDPR\Observer\AbstractCustomerObserver;
use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Helper\Checkboxes as CheckboxesHelper;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory as RemovalResourceFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory as RemovalCollectionFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests as RemovalResource;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\Collection as RemovalCollection;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;

/**
 * Class CustomerLogin
 */
class CustomerLogin extends AbstractCustomerObserver
{
    /**
     * @var RemovalResourceFactory
     */
    private $removalResourceFactory;

    /**
     * @var RemovalCollectionFactory
     */
    private $collectionFactory;

    /**
     * CustomerLogin constructor.
     * @param DataHelper $dataHelper
     * @param CheckboxesHelper $checkboxesHelper
     * @param MessageManagerInterface $messageManager
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ConfigInterface $sessionConfig
     * @param RemovalResourceFactory $removalResourceFactory
     * @param RemovalCollectionFactory $collectionFactory
     */
    public function __construct(
        DataHelper $dataHelper,
        CheckboxesHelper $checkboxesHelper,
        MessageManagerInterface $messageManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigInterface $sessionConfig,
        RemovalResourceFactory $removalResourceFactory,
        RemovalCollectionFactory $collectionFactory
    ) {
        $this->removalResourceFactory = $removalResourceFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($dataHelper, $checkboxesHelper, $messageManager, $cookieManager, $cookieMetadataFactory, $sessionConfig);
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $observer->getData('customer');

        if ($customer && $this->dataHelper->moduleEnabled()) {
            $this->cancelAllRemovalRequests($customer);
            $this->logCookiesConsents($customer);
        }
    }

    /**
     * @param $customer
     * @return $this
     */
    private function cancelAllRemovalRequests($customer)
    {
        if (! $customer || ! $customer->getId()) {
            return $this;
        }

        /** @var RemovalCollection $removalRequests */
        $removalRequests = $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
            ->addFieldToFilter('status', ['eq' =>  RemovalStatus::PENDING]);

        if ($removalRequests->getSize()) {
            foreach ($removalRequests->getItems() as $removalRequest) {
                /** @var RemovalRequests $removalRequest */
                $removalRequest->addData([
                    'cancelled_at' => $this->checkboxesHelper->getFormattedGmtDateTime(),
                    'cancelled_by' => 'Customer',
                    'scheduled_at' => null,
                    'status' => RemovalStatus::CANCELLED
                ]);
                /** @var RemovalResource $removalRequestResource */
                $removalRequestResource = $this->removalResourceFactory->create();
                $removalRequestResource->save($removalRequest);
            }

            $this->messageManager->addSuccessMessage(
                __("Congratulations! You have reactivated your account.")
            );
        }

        return $this;
    }
}
