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

namespace Plumrocket\GDPR\Helper;

use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Plumrocket\GDPR\Helper\Data;

/**
 * Helper to get account specific data.
 */
class CustomerData extends AbstractHelper
{
    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * AccountData constructor.
     *
     * @param Context $context
     * @param AuthenticationInterface $authentication
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        OrderCollectionFactory $orderCollectionFactory,
        Data $helper
    ) {
        parent::__construct($context);

        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->helper = $helper;
    }

    /**
     * Check if customer has opened orders.
     *
     * @param $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasOpenedOrders($customerId)
    {
        $customerId = (int)$customerId;

        if (! $customerId) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Error. Customer ID is missing.')
            );
        }

        $orderCollection = $this->orderCollectionFactory->create($customerId)
            ->addFieldToFilter('state', ['nin' => ['canceled', 'closed', 'complete']]);

        return (bool) $orderCollection->getTotalCount();
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * Authenticate user.
     *
     * @param CustomerInterface $currentCustomerDataObject
     *
     * @return void
     * @throws InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\State\UserLockedException
     */
    public function authenticate(CustomerInterface $currentCustomerDataObject, $password)
    {
        try {
            $this->authentication
                ->authenticate($currentCustomerDataObject->getId(), $password);
        } catch (InvalidEmailOrPasswordException $e) {
            throw new InvalidEmailOrPasswordException(__('The password you entered is incorrect. Please try again.'));
        }
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getAnonymousString(int $customerId)
    {
        return $this->helper->getAnonymizationKey()."-".$customerId;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getAnonymousEmail(int $customerId)
    {
        return $this->getAnonymousString($customerId)."@example.com";
    }

    /**
     * @param $data
     * @param int $customerId
     * @return array
     */
    public function getDataAnonymized($data, int $customerId)
    {
        $dataAnonymized = [];

        if (! empty($data) && is_array($data)) {
            foreach ($data as $field => $value) {
                switch ($value) {
                    case 'anonymousString':
                        $dataAnonymized[$field] = $this->getAnonymousString($customerId);
                        break;
                    case 'anonymousEmail':
                        $dataAnonymized[$field] = $this->getAnonymousEmail($customerId);
                        break;
                    default:
                        $dataAnonymized[$field] = $value;
                }
            }
        }

        return $dataAnonymized;
    }
}
