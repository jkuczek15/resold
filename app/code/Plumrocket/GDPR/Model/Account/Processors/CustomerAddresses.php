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
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\CountryFactory;

/**
 * Processor customer addresses.
 */
class CustomerAddresses implements DataProcessorInterface
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerAddresses constructor.
     *
     * @param Customer $customer
     * @param CountryFactory $countryFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $dataExport
     */
    public function __construct(
        Customer $customer,
        CountryFactory $countryFactory,
        AddressRepositoryInterface $addressRepository,
        CustomerRepositoryInterface $customerRepository,
        array $dataExport = []
    ) {
        $this->customer = $customer;
        $this->countryFactory = $countryFactory;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
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
     */
    public function export(CustomerInterface $customer)
    {
        $addresses = $this->customer->load($customer->getId())->getAddresses();
        $returnData = [];
        $i=0;

        if (!$addresses) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($addresses as $address) {
            $address->setData(
                'country_id',
                $this->countryFactory->create()->loadByCode($address->getCountryId())->getName()
            );

            $addressData = $address->getData();

            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = (isset($addressData[$key]) ? $addressData[$key] : '');
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        $customer = $this->customerRepository->getById($customer->getId());
        $addresses = $customer->getAddresses();

        if (!$addresses) {
            return;
        }

        foreach ($addresses as $address) {
            $this->addressRepository->delete($address);
        }
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
}
