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
use Plumrocket\GDPR\Helper\CustomerData as CustomerDataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Processor customer order items.
 */
class CustomerOrderItems implements DataProcessorInterface
{
    /**
     * @var CustomerDataHelper
     */
    private $customerData;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerOrders constructor.
     *
     * @param CustomerDataHelper $customerData
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param array $dataExport
     */
    public function __construct(
        CustomerDataHelper $customerData,
        OrderCollectionFactory $orderCollectionFactory,
        array $dataExport = []
    ) {
        $this->customerData = $customerData;
        $this->orderCollectionFactory = $orderCollectionFactory;
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
        $orderCollection = $this->orderCollectionFactory->create($customer->getId());
        $returnData = [];
        $i=0;

        if (!$orderCollection->getSize()) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;
        
        foreach ($orderCollection as $order) {
            $orderItems = $order->getAllVisibleItems();

            foreach ($orderItems as $item) {
                $item->setData('increment_id', '#'.$order->getIncrementId());
                $item->setData('price', $order->getOrderCurrency()->formatPrecision($item->getPrice(), 2, [], false));
                $item->setData(
                    'row_total',
                    $order->getOrderCurrency()->formatPrecision($item->getRowTotal(), 2, [], false)
                );

                switch ($item->getProductType()) {
                    case 'configurable':
                        $product_options = $item->getProductOptions();
                        $product_options_combine = [];

                        foreach ($product_options['attributes_info'] as $attribute_info) {
                            $product_options_combine[] = $attribute_info['label'].": ".$attribute_info['value'];
                        }

                        $item->setData('product_options', implode(", ", $product_options_combine));
                        break;
                    case 'bundle':
                        $product_options = $item->getProductOptions();
                        $product_options_combine = [];

                        foreach ($product_options['bundle_options'] as $bundle_info) {
                            $bundle_price = $order->getOrderCurrency()->formatPrecision(
                                $bundle_info['value'][0]['price'],
                                2,
                                [],
                                false
                            );
                            $product_options_combine[] = $bundle_info['value'][0]['qty']
                                . "x "
                                . $bundle_info['value'][0]['title']
                                . " " . $bundle_price;
                        }

                        $item->setData('product_options', implode(", ", $product_options_combine));
                        break;
                    default:
                        $item->setData('product_options', '');
                        break;
                }

                $itemData = $item->getData();

                foreach ($this->dataExport as $key => $title) {
                    $returnData[$i][] = (isset($itemData[$key]) ? $itemData[$key] : '');
                }

                $i++;
            }
        }

        return $returnData;
    }

    /**
     * Executed upon order items deletion.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function delete(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        // empty function
    }

    /**
     * Executed upon order items anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function anonymize(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        // empty function
    }
}
