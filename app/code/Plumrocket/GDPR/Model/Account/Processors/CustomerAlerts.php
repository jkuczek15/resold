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
use Magento\ProductAlert\Model\ResourceModel\Price\CollectionFactory as PriceCollectionFactory;
use Magento\ProductAlert\Model\ResourceModel\Stock\CollectionFactory as StockCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Processor customer alerts.
 */
class CustomerAlerts implements DataProcessorInterface
{
    /**
     * @var PriceCollectionFactory
     */
    private $priceCollectionFactory;

    /**
     * @var StockCollectionFactory
     */
    private $stockCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerWishlist constructor.
     *
     * @param PriceCollectionFactory $priceCollectionFactory
     * @param StockCollectionFactory $stockCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $dataExport
     */
    public function __construct(
        PriceCollectionFactory $priceCollectionFactory,
        StockCollectionFactory $stockCollectionFactory,
        ProductRepositoryInterface $productRepository,
        array $dataExport = []
    ) {
        $this->priceCollectionFactory = $priceCollectionFactory;
        $this->stockCollectionFactory = $stockCollectionFactory;
        $this->productRepository = $productRepository;
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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function export(CustomerInterface $customer)
    {
        $priceCollection = $this->priceCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->load();

        $stockCollection = $this->stockCollectionFactory
            ->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->load();

        $returnData = [];
        $i=0;

        if (!$priceCollection->getSize() && !$stockCollection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        if ($priceCollection->getSize()) {
            foreach ($priceCollection as $priceAlert) {
                $priceAlert->setData('type', 'Price Alert');
                $product = $this->productRepository->getById($priceAlert->getProductId());
                $priceAlert->setData('product_name', $product->getName());
                $priceAlert->setData('product_sku', $product->getSku());
                $priceAlertData = $priceAlert->getData();
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = (isset($priceAlertData[$key]) ? $priceAlertData[$key] : '');
                }
                $i++;
            }
        }

        if ($stockCollection->getSize()) {
            foreach ($stockCollection as $stockAlert) {
                $stockAlert->setData('type', 'Stock Alert');
                $product = $this->productRepository->getById($stockAlert->getProductId());
                $stockAlert->setData('product_name', $product->getName());
                $stockAlert->setData('product_sku', $product->getSku());
                $stockAlertData = $stockAlert->getData();
                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = (isset($stockAlertData[$key]) ? $stockAlertData[$key] : '');
                }
                $i++;
            }
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
    public function delete(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        // empty function
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
