<?php
/**
 * Resold
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category    Resold
 * @package     Resold
 * @author      Resold Core Team <dev@resold.us>
 * @copyright   Copyright Resold (https://resold.us/)
 * @license     https://resold.us/license-agreement
 */
namespace Resold\Api\Model;
use Magento\Sales\Model\Order;

class WebhookManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Resold\Api\Logger\Logger $logger,
    \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order
   )
  {
    $this->logger = $logger;
    $this->order = $order;
  }

	/**
	 * {@inheritdoc}
	 */
  public function processPostmatesEvent($kind, $id, $delivery_id, $status, $data, $created, $live_mode)
  {
    // log the event
    $this->logger->info(json_encode([
      'type' => 'PostmatesEvent',
      'kind' => $kind,
      'id' => $id,
      'delivery_id' => $delivery_id,
      'status' => $status,
      'data' => $data,
      'created' => $created,
      'live_mode' => $live_mode
    ]));

    if(isset($data['manifest']) && isset($data['manifest']['reference']) && $data['manifest']['reference'] !== null) {
      // process the event
      $productId = $data['manifest']['reference'];

      $orderCollection = $this->order->create();
      $orderCollection->getSelect()
            ->join(
                'sales_order_item',
                'main_table.entity_id = sales_order_item.order_id'
            )->where('product_id = '.$productId);
      $orderCollection->getSelect()->group('main_table.entity_id');

      $orderState = Order::STATE_COMPLETE;
      foreach ($orderCollection as $order) {
        $order->setState($orderState)->setStatus(Order::STATE_COMPLETE);
        $order->save();
        $this->logger->info('order #: '.$order->getIncrementId());
      }// end foreach over orders

    }// end if we have a valid product ID

  }// end function processPostmatesEvent
}
