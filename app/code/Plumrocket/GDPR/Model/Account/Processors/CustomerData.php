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

namespace Plumrocket\GDPR\Model\Account\Processors;

use Plumrocket\GDPR\Api\DataProcessorInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Security\Model\ResourceModel\PasswordResetRequestEvent\CollectionFactory as PasswordResetCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Newsletter\Model\Subscriber;

/**
 * Processor customer data.
 */
class CustomerData implements DataProcessorInterface
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var PasswordResetCollectionFactory
     */
    private $passwordResetCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerData constructor.
     *
     * @param Customer $customer
     * @param CustomerRepositoryInterface $customerRepository
     * @param PasswordResetCollectionFactory $passwordResetCollectionFactory
     * @param ResourceConnection $resourceConnection
     * @param Subscriber $subscriber
     * @param array $dataExport
     */
    public function __construct(
        Customer $customer,
        CustomerRepositoryInterface $customerRepository,
        PasswordResetCollectionFactory $passwordResetCollectionFactory,
        ResourceConnection $resourceConnection,
        Subscriber $subscriber,
        array $dataExport = []
    ) {
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->passwordResetCollectionFactory = $passwordResetCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->subscriber = $subscriber;
        $this->dataExport = $dataExport;
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function export(CustomerInterface $customer)
    {
        $customer = $this->customer->load($customer->getId());
        $dataTitles = $dataValues = [];

        if ($customer->getGender()) {
            $genders = [1 => 'Male', 2 => 'Female', 3 => 'Not Specified'];
            $customer->setData('gender', $genders[$customer->getGender()]);
        }

        $newsletter_subscribe = "No";
        $checkSubscriber = $this->subscriber->loadByCustomerId($customer->getId());
        if ($checkSubscriber->isSubscribed()) {
            $subscriber_statuses = [1 => 'Subscribed', 2 => 'Not Activated', 3 => 'Unsubscribed', 4 => 'Unconfirmed'];
            $newsletter_subscribe = $subscriber_statuses[$checkSubscriber->getStatus()];
        }
        $customer->setData('newsletter_subscribe', $newsletter_subscribe);

        $customerData = $customer->getData();
        foreach ($this->dataExport as $key => $title) {
            $dataTitles[] = $title;
            $dataValues[] = (isset($customerData[$key]) ? $customerData[$key] : '');
        }

        return [$dataTitles, $dataValues];
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();

        $passwordReset = $this->passwordResetCollectionFactory->create()
            ->filterByAccountReference($customerEmail);
        if ($passwordReset->getSize()) {
            $passwordReset->walk('delete');
        }

        $this->deleteDataFromTable('email_contact', 'customer_id', $customerId);
        $this->deleteDataFromTable('email_review', 'customer_id', $customerId);
        $this->deleteDataFromTable('email_automation', 'email', $customerEmail);
        $this->deleteDataFromTable('email_campaign', 'customer_id', $customerId);

        $this->customerRepository->deleteById($customerId);
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     */
    public function anonymize(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        // empty function
    }

    /**
     * Delete data any table.
     *
     * @param $table
     * @param $idField
     * @param $value
     */
    public function deleteDataFromTable($table, $idField, $value)
    {
        $tableName  = $this->resourceConnection->getTableName($table);
        $connection = $this->resourceConnection->getConnection();
        if ($connection->isTableExists($tableName)) {
            $connection->delete($tableName, [$idField.' = ?' => $value]);
        }
    }
}
