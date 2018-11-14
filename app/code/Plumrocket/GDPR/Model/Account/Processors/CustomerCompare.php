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
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Processor customer wishlist.
 */
class CustomerCompare implements DataProcessorInterface
{
    /**
     * Product compare item collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerWishlist constructor.
     *
     * @param CollectionFactory $itemCollectionFactory
     * @param Compare $compareHelper
     * @param array $dataExport
     */
    public function __construct(
        CollectionFactory $itemCollectionFactory,
        Compare $compareHelper,
        array $dataExport = []
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->compareHelper = $compareHelper;
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
        $compareData = $this->itemCollectionFactory
            ->create()
            ->useProductItem(true)
            ->setCustomerId($customer->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->load();

        $returnData = [];
        $i=0;

        if (!$compareData->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        foreach ($compareData as $item) {
            $itemData = $item->getData();

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
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processCompare($customer->getId());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->processCompare($customer->getId());
    }

    /**
     * Delete customer compare items.
     *
     * @param int $customerId
     *
     * @return void
     */
    private function processCompare(int $customerId)
    {
        $this->itemCollectionFactory
            ->create()
            ->setCustomerId($customerId)
            ->clear();
        $this->compareHelper->calculate();
    }
}
