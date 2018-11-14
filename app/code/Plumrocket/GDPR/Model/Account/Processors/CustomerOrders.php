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
use \Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Framework\App\ResourceConnection;

/**
 * Processor customer orders.
 */
class CustomerOrders implements DataProcessorInterface
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
     * @var AddressRenderer
     */
    private $addressRenderer;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * @var array
     */
    private $dataAnonymize;

    /**
     * @var array
     */
    private $dataAnonymizeAddresses;

    /**
     * @var array
     */
    private $dataAnonymizeGrids;

    /**
     * CustomerOrders constructor.
     *
     * @param CustomerDataHelper $customerData
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param AddressRenderer $addressRenderer
     * @param ResourceConnection $resourceConnection
     * @param array $dataExport
     * @param array $dataAnonymize
     * @param array $dataAnonymizeAddresses
     * @param array $dataAnonymizeGrids
     */
    public function __construct(
        CustomerDataHelper $customerData,
        OrderCollectionFactory $orderCollectionFactory,
        AddressRenderer $addressRenderer,
        ResourceConnection $resourceConnection,
        array $dataExport = [],
        array $dataAnonymize = [],
        array $dataAnonymizeAddresses = [],
        array $dataAnonymizeGrids = []
    ) {
        $this->customerData = $customerData;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->addressRenderer = $addressRenderer;
        $this->resourceConnection     = $resourceConnection;
        $this->dataExport = $dataExport;
        $this->dataAnonymize = $dataAnonymize;
        $this->dataAnonymizeAddresses = $dataAnonymizeAddresses;
        $this->dataAnonymizeGrids = $dataAnonymizeGrids;
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
            $order->setData('increment_id', '#'.$order->getIncrementId());
            $order->setData('billing_adddress', $this->addressRenderer->format($order->getBillingAddress(), 'text'));
            $shipping_adddress = $order->getShippingAddress();
            $order->setData(
                'shipping_adddress',
                (($shipping_adddress) ? $this->addressRenderer->format($shipping_adddress, 'text') : '')
            );
            $order->setData(
                'payment_method',
                $order->getPayment()->getMethodInstance()->getTitle()
            );
            $order->setData(
                'grand_total',
                $order->getOrderCurrency()->formatPrecision($order->getGrandTotal(), 2, [], false)
            );

            $orderData = $order->getData();
            foreach ($this->dataExport as $key => $title) {
                $returnData[$i][] = (isset($orderData[$key]) ? $orderData[$key] : '');
            }
            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer orders deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processOrders($customer->getId());
    }

    /**
     * Executed upon customer orders anonymization.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    public function anonymize(CustomerInterface $customer)// @codingStandardsIgnoreLine
    {
        // empty function
    }

    /**
     * Process orders.
     *
     * @param int $customerId
     * @return void
     */
    protected function processOrders(int $customerId)// @codingStandardsIgnoreLine
    {
        $orderCollection = $this->orderCollectionFactory->create($customerId);

        if (!$orderCollection->getTotalCount()) {
            return;
        }

        $dataAnonymized = $this->customerData->getDataAnonymized($this->dataAnonymize, $customerId);

        if (!empty($dataAnonymized) && $orderCollection->getSize()) {
            $orderCollection->setDataToAll($dataAnonymized)->save();
        }

        $dataAnonymizeAddresses = $this->customerData->getDataAnonymized($this->dataAnonymizeAddresses, $customerId);
        $orderIds = [];

        foreach ($orderCollection as $order) {
            $orderIds[] = $order->getId();
            $addressesCollection = $order->getAddressesCollection();
            if (!empty($dataAnonymizeAddresses) && $addressesCollection->getSize()) {
                $addressesCollection->setDataToAll($dataAnonymizeAddresses)->save();
            }
        }

        if (!empty($orderIds)) {
            //anonymize data in grids
            $dataAnonymizeGrids = $this->customerData->getDataAnonymized($this->dataAnonymizeGrids, $customerId);
            $this->updateDataInTable('sales_order_grid', 'entity_id', $orderIds, $dataAnonymizeGrids);
            $this->updateDataInTable('sales_shipment_grid', 'order_id', $orderIds, $dataAnonymizeGrids);

            unset($dataAnonymizeGrids['shipping_name']);
            $this->updateDataInTable('sales_invoice_grid', 'order_id', $orderIds, $dataAnonymizeGrids);
            $this->updateDataInTable('sales_creditmemo_grid', 'order_id', $orderIds, $dataAnonymizeGrids);
        }
    }

    /**
     * Update any table.
     *
     * @param $table
     * @param $idField
     * @param $ids
     * @param $data
     */
    public function updateDataInTable($table, $idField, $ids, $data)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName  = $this->resourceConnection->getTableName($table);

        $connection->update(
            $tableName,
            $data,
            [$idField . ' IN (?)' => $ids]
        );
    }
}
