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

namespace Plumrocket\GDPR\Model\Account\Processors\Plumrocket;

use Plumrocket\GDPR\Api\DataProcessorInterface;
use Plumrocket\GDPR\Model\ResourceModel\ConsentsLog\CollectionFactory as ConsentsLogCollectionFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequests\CollectionFactory as RemovalRequestsCollectionFactory;
use Plumrocket\GDPR\Helper\CustomerData;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Processor Gdpr.
 */
class Gdpr implements DataProcessorInterface
{
    /**
     * @var ConsentsLogCollectionFactory
     */
    private $consentsLogCollectionFactory;

    /**
     * @var RemovalRequestsCollectionFactory
     */
    private $removalRequestsCollectionFactory;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepositoryInterface;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * GdprConsentsLog constructor.
     *
     * @param ConsentsLogCollectionFactory $consentsLogCollectionFactory
     * @param RemovalRequestsCollectionFactory $removalRequestsCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param array $dataExport
     */
    public function __construct(
        ConsentsLogCollectionFactory $consentsLogCollectionFactory,
        RemovalRequestsCollectionFactory $removalRequestsCollectionFactory,
        CustomerData $customerData,
        StoreManagerInterface $storeManager,
        PageRepositoryInterface $pageRepositoryInterface,
        array $dataExport = [],
        array $dataAnonymize = []
    ) {
        $this->consentsLogCollectionFactory = $consentsLogCollectionFactory;
        $this->removalRequestsCollectionFactory = $removalRequestsCollectionFactory;
        $this->customerData = $customerData;
        $this->storeManager = $storeManager;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->dataExport = $dataExport;
        $this->dataAnonymize = $dataAnonymize;
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     *
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        $collection = $this->consentsLogCollectionFactory->create()->addFieldToFilter('customer_id', $customerId);
        $returnData = [];
        $i=0;

        if (! $collection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($collection as $item) {
            $itemData = $item->getData();

            if (! empty($itemData['cms_page_id'])) {
                try {
                    $itemData['cms_page'] = $this->pageRepositoryInterface->getById(
                        $itemData['cms_page_id']
                    )->getTitle();
                } catch (\Exception $e) {
                    $itemData['cms_page'] = '';
                }
            } else {
                $itemData['cms_page'] = '';
            }

            try {
                $itemData['website'] = $this->storeManager->getWebsite($itemData['website_id'])->getName();
            } catch (\Exception $e) {
                $itemData['website'] = '';
            }

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = (isset($itemData[$key]) ? $itemData[$key] : '');
            }

            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        $this->anonymize($customer);
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        $customerId = $customer->getId();
        $removalRequests = $this->removalRequestsCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId]);

        $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customerId);
        if (!empty($dataAnonymized) && $removalRequests->getSize()) {
            $removalRequests->setDataToAll($dataAnonymized)->save();
        }
    }
}
