<?php
/**
 * Copyright © 2016 Resold. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Custom\Review\Block;

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

    public function getVendor()
    {
        $vendorId = $_GET['seller_id'];
        return $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
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
