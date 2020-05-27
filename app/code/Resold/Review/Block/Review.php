<?php
/**
 * Copyright © 2016 Resold. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Resold\Review\Block;

use Ced\CsMessaging\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Backend\Block\Template\Context;

class Review extends \Magento\Framework\View\Element\Template
{
    protected $_vendor;

    protected $_storeManager;

    protected $_session;

    protected $request;

    public $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->_objectManager=$objectManager;
        $this->_session = $customerSession;
    }

    public function getOrder()
    {
      $order_id = $_GET['id'];
      return $this->orderRepository->get($order_id);
    }


    public function hasExistingReview()
    {
      $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
      $connection = $resource->getConnection();

      if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
        return null;
      }// end if id invalid

      $order_id = $_GET['id'];
      $order = $this->orderRepository->get($order_id);
      $order_number = $order->getIncrementId();

      // Get vendor by order number
      $sql = "SELECT vendor_id FROM MagentoQuickstartDB.ced_csvendorreview_review WHERE value = '".$this->escapeQuote($order_id)."'";
      $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.

      return count($result) > 0;
    }

    public function getVendor()
    {
      $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
      $connection = $resource->getConnection();

      if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
        return null;
      }// end if id invalid

      $order_id = $_GET['id'];
      $order = $this->orderRepository->get($order_id);
      $order_number = $order->getIncrementId();

      // Get vendor by order number
      $sql = "SELECT vendor_id FROM ced_csmarketplace_vendor_sales_order WHERE order_id =  '".$this->escapeQuote($order_number)."'";
      $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.

      if(count($result) == 0){
        return null;
      }// end if no vendor found

      $vendor_id = $result[0]['vendor_id'];
      return $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendor_id);
    }

    public function getProduct()
    {
      $productId = $_GET['product_id'];
      return $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    }

    public function getVendorId()
    {
        return $this->getVendor()->getId();
    }

    public function getRatingOption()
    {
        return [
        '0'        => __('Please Select Option'),
        '20'    => __('1 OUT OF 5'),
        '40'    => __('2 OUT OF 5'),
        '60'    => __('3 OUT OF 5'),
        '80'    => __('4 OUT OF 5'),
        '100'    => __('5 OUT OF 5')
        ];
    }

    public function getRatings()
    {
        $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection();
        return $rating;
    }

    public function getAction()
    {
        return $this->getUrl('csvendorreview/rating/post');
    }

    public function getSession()
    {
      return $this->_session;
    }

    public function getCustomerId()
    {
      return $this->_session->getId();
    }
}
