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

use \DateTime;
use \Google\Cloud\Firestore\FirestoreClient;
use \Kreait\Firebase\Messaging\CloudMessage;
use \Kreait\Firebase\ServiceAccount;

class WebhookManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Resold\Api\Logger\Logger $logger,
    \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $order,
    \Kreait\Firebase\Factory $factory
   )
  {
    try {
      putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/firebase-adminsdk-key.json');
      $this->logger = $logger;
      $this->order = $order;
      $this->firestoreClient = new FirestoreClient();
      $this->factory = $factory->withServiceAccount(ServiceAccount::fromJsonFile('/var/www/html/firebase-adminsdk-key.json'));
    } catch(\Exception $e) {
      $this->logger->info($e->getMessage());
    }
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
    try {
      if(isset($data['manifest']) && isset($data['manifest']['reference']) && $data['manifest']['reference'] !== null) {
        // firebase messaging
        $messaging = $this->factory->createMessaging();

        // process the event
        $reference = $data['manifest']['reference'];
        $referenceParts = explode('|', $reference);
        $productId = $referenceParts[0];
        $buyerCustomerId = $referenceParts[1];
        $sellerCustomerId = $referenceParts[2];
        $buyerDeviceToken = $sellerDeviceToken = '';

        // load product by ID
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);

        // fetch the buyer's device token
        $buyerRef = $this->firestoreClient->collection('users')->document($buyerCustomerId);
        $buyerSnapshot = $buyerRef->snapshot();
        if($buyerSnapshot->exists()) {
          $buyerDoc = $buyerSnapshot->data();
          $buyerDeviceToken = $buyerDoc['deviceToken'];
        }// end if buyer exists

        // fetch the seller's device token
        $sellerRef = $this->firestoreClient->collection('users')->document($sellerCustomerId);
        $sellerSnapshot = $sellerRef->snapshot();
        if($sellerSnapshot->exists()) {
          $sellerDoc = $sellerSnapshot->data();
          $sellerDeviceToken = $sellerDoc['deviceToken'];
        }// end if buyer exists

        // fetch orders for this product
        $orderCollection = $this->order->create();
        $orderCollection->getSelect()
              ->join(
                  'sales_order_item',
                  'main_table.entity_id = sales_order_item.order_id'
              )->where('product_id = '.$productId);
        $orderCollection->getSelect()->group('main_table.entity_id');
        $order = $orderCollection->getFirstItem();

        // set order status based on Postmates status
        $orderStatus = 'processing';
        switch($status) {
          case 'pending': break;
          case 'pickup': 
            $orderStatus = 'pickup';
            if($data['courier_imminent'] == true) {
              // send notification that driver is approaching pickup
              $approachingMessage = CloudMessage::withTarget('token', $sellerDeviceToken)->withNotification([
                'title' => 'Driver is approaching to pickup your '. $product->getName(),
                'body' => '',
                'image' => $product->getThumbnail()
              ])->withData([
                'image' => $product->getThumbnail(),
                'orderId' => $order->getId(),
                'productId' => $product->getId(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
              ]);
              $messaging->send($approachingMessage);
            }// end if courier imminent
            break;
          case 'pickup_complete':
          case 'ongoing':
          case 'dropoff':
            $orderStatus = 'delivery_in_progress';
            if($data['courier_imminent'] == true) {
              // send notification that driver is approaching dropoff
              $approachingMessage = CloudMessage::withTarget('token', $buyerDeviceToken)->withNotification([
                'title' => 'Driver is approaching to dropoff your '. $product->getName(),
                'body' => '',
                'image' => $product->getThumbnail()
              ])->withData([
                'image' => $product->getThumbnail(),
                'orderId' => $order->getId(),
                'productId' => $product->getId(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
              ]);
              $messaging->send($approachingMessage);
            }// end if courier imminent
            break;
          case 'delivered':
          case 'returned':
            $orderStatus = 'complete';
            break;
          default: break;
        }// end switch case settings order status

        $oldStatus = $order->getStatus();
        $now = new DateTime();
        $pickup_eta = new DateTime($data['pickup_eta']);
        $dropoff_eta = new DateTime($data['dropoff_eta']);
        $pickup_difference = $pickup_eta->diff($now);
        $dropoff_difference = $dropoff_eta->diff($now);

        switch($oldStatus) {
          case 'processing':
            if($orderStatus == 'pickup') {
                // send notification that driver is on the way to pickup
                $sellerMessage = CloudMessage::withTarget('token', $sellerDeviceToken)->withNotification([
                  'title' => 'Driver is on the way to pickup your '. $product->getName(),
                  'body' => 'Arriving in '.$pickup_difference->i.' minutes',
                  'image' => $product->getThumbnail()
                ])->withData([
                  'image' => $product->getThumbnail(),
                  'orderId' => $order->getId(),
                  'productId' => $product->getId(),
                  'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);
                $buyerMessage = CloudMessage::withTarget('token', $buyerDeviceToken)->withNotification([
                  'title' => 'Driver is on the way to deliver your '. $product->getName(),
                  'body' => 'Arriving in '.$dropoff_difference->i.' minutes',
                  'image' => $product->getThumbnail()
                ])->withData([
                  'image' => $product->getThumbnail(),
                  'orderId' => $order->getId(),
                  'productId' => $product->getId(),
                  'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]);
                $messaging->send($sellerMessage);
                $messaging->send($buyerMessage);
            }// end if driver on the way to pickup
            break;
          case 'pickup': 
            if($orderStatus == 'delivery_in_progress') {
              // send notification that driver is on the way to deliver
              $buyerMessage = CloudMessage::withTarget('token', $buyerDeviceToken)->withNotification([
                'title' => 'Driver has picked up your '. $product->getName(),
                'body' => 'Arriving in '.$dropoff_difference->i.' minutes',
                'image' => $product->getThumbnail()
              ])->withData([
                'image' => $product->getThumbnail(),
                'orderId' => $order->getId(),
                'productId' => $product->getId(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
              ]);
              $messaging->send($buyerMessage);
            }// end if driver on the way to deliver
            break;
          case 'delivery_in_progress':
            if($orderStatus == 'complete') {
              // send notification that driver has delivered the item
              $sellerMessage = CloudMessage::withTarget('token', $sellerDeviceToken)->withNotification([
                'title' => 'Driver has dropped off your '. $product->getName(),
                'body' => '',
                'image' => $product->getThumbnail()
              ])->withData([
                'image' => $product->getThumbnail(),
                'orderId' => $order->getId(),
                'productId' => $product->getId(),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
              ]);
              $messaging->send($sellerMessage);
            }// end if driver has delivered the item
            break;
          default: break;
        }// end switch case settings order status

        $order->setState($orderStatus)->setStatus($orderStatus);
        $order->setPickupEta($data['pickup_eta']);
        $order->setDropoffEta($data['dropoff_eta']);
        $order->save();

      }// end if we have a valid product ID
    } catch (\Exception $e) {
      $this->logger->info($e->getMessage());
    }// end try-catch
  }// end function processPostmatesEvent
}
