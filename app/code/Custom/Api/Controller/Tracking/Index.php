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
namespace Custom\Api\Controller\Tracking;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'report/email/email_template';

    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'contact/contact/enabled';

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->trackFactory = $trackFactory;
        $this->scopeConfig = $scopeConfig;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $resultRedirect = $this->resultRedirectFactory->create();
      ####################################
      // REQUEST AND USER VALIDATON
      ###################################
      // Ensure valid request and protect against CSRF
      if (!$this->formKeyValidator->validate($this->getRequest())) {
        return $this->resultJsonFactory->create()->setData(['error' => 'Invalid Request.']);
      }// end if valid request

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      if(!isset($post['tracking_number']) || $post['tracking_number'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if product id is set

      if(!isset($post['order_id']) || $post['order_id'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if product id is set

      $order_id = $post['order_id'];
      $tracking_number = $post['tracking_number'];
      $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($order_id);

      if (!$order->canShip()) {
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if cant ship the order

      // Initialize the order shipment object
      $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
      $shipment = $convertOrder->toShipment($order);

      // Loop through order items (only 1)
      foreach ($order->getAllItems() as $orderItem) {
          // Check if order item has qty to ship or is virtual
          if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
              continue;
          }

          $qtyShipped = $orderItem->getQtyToShip();

          // Create shipment item with qty
          $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

          // Add shipment item to shipment
          $shipment->addItem($shipmentItem);
      }// end foreach loop over order items

      // Register shipment
      $shipment->register();
      $shipment->getOrder()->setIsInProcess(true);

      try {
          $seller = $this->getVendor($order);
          $data = array(
              'carrier_code' => 'custom',
              'title' => $seller->getName(),
              'number' => $tracking_number
          );
          $track = $this->trackFactory->create()->addData($data);
          $shipment->addTrack($track)->save();

          // Save created shipment and order
          $shipment->save();
          $shipment->getOrder()->save();

          // Send email
          $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);

          $shipment->save();
          $this->messageManager->addSuccess(
            __('You have successfully sent a tracking number.')
          );
      } catch (\Exception $e) {
          throw new \Magento\Framework\Exception\LocalizedException(
              __($e->getMessage())
          );
      }

      return $resultRedirect->setPath('sales/order/view/order_id/'.$post['order_id']);
    }// end function execute

    public function getVendor($order)
    {
      $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
      $connection = $resource->getConnection();
      $post = $this->getRequest()->getPostValue();

      if(!isset($post['order_id']) || !is_numeric($post['order_id'])){
        return null;
      }// end if id invalid

      // Get vendor by order number
      $order_number = $order->getIncrementId();
      $sql = "SELECT vendor_id FROM ced_csmarketplace_vendor_sales_order WHERE order_id =  '".$order_number."'";
      $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.

      if(count($result) == 0){
        return null;
      }// end if no vendor found

      $vendor_id = $result[0]['vendor_id'];
      return $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendor_id);
    }
}
