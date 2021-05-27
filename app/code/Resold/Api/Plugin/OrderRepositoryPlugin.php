<?php
namespace Resold\Api\Plugin;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add "customer_feedback" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $pickupEta = $order->getData('pickup_eta');
        $dropoffEta = $order->getData('dropoff_eta');
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setPickupEta($pickupEta);
        $extensionAttributes->setDropoffEta($dropoffEta);
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    /**
     * Add "customer_feedback" extension attribute to order data object to make it accessible in Magento API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            $pickupEta = $order->getData('pickup_eta');
            $dropoffEta = $order->getData('dropoff_eta');
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setPickupEta($pickupEta);
            $extensionAttributes->setDropoffEta($dropoffEta);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }
}
