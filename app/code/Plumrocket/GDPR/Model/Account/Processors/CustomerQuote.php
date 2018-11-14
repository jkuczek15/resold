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
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Processor customer quote data.
 */
class CustomerQuote implements DataProcessorInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerQuote constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param array $dataExport
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $dataExport = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->storeManager = $storeManager;
        $this->priceHelper = $priceHelper;
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
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        try {
            $collection = $this->cartRepository->getForCustomer($customer->getId())->getItems();
        } catch (NoSuchEntityException $e) {
            return null;
        }
        
        $returnData = [];
        $i=0;

        if (!$collection) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;
        
        foreach ($collection as $cartItem) {
            $cartItem->setData('price', $this->priceHelper->currency((float)$cartItem->getPrice(), true, false));

            $cartItemData = $cartItem->getData();
            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = (isset($cartItemData[$key]) ? $cartItemData[$key] : '');
            }
            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processQuote($customer->getId());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->processQuote($customer->getId());
    }

    /**
     * Process quote.
     *
     * @param int $customerId
     * @return void
     */
    private function processQuote(int $customerId)
    {
        try {
            $quote = $this->cartRepository->getForCustomer($customerId, $this->getStoreIds());
        } catch (NoSuchEntityException $e) {
            return;
        }

        $this->cartRepository->delete($quote);
    }

    /**
     * Get store ids.
     *
     * @return array
     */
    private function getStoreIds()
    {
        $ids = [];

        foreach ($this->storeManager->getStores() as $store) {
            $ids[] = $store->getId();
        }

        return $ids;
    }
}
