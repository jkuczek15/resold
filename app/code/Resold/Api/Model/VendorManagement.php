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

class VendorManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Magento\Authorization\Model\UserContextInterface $userContext,
    \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
    \Ced\CsMarketplace\Model\Vendor $vendor,
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
    \Magento\Framework\App\ResourceConnection $resource
  )
  {
      $this->userContext = $userContext;
      $this->vendorFactory = $vendorFactory;
      $this->vendor = $vendor;
      $this->resource = $resouce;
      $this->customerRepository = $customerRepository;
  }

	/**
	 * {@inheritdoc}
	 */
  public function createVendor()
	{
    $customerId = $this->userContext->getUserId();

    // create a mew vendor/seller account
    $vendorModel = $this->vendorFactory->create();

    // change the user group
    $customer = $this->customerRepository->getById($customerId);
    $customer->setGroupId(5);
    $this->customerRepository->save($customer);

    $vendor = $vendorModel->setCustomer($customer)->register([
      'public_name' => $customer->getFirstname().' '.$customer->getLastname(),
      'shop_url' => uniqid()
    ]);
    $vendor->setGroup('general');
    if (!$vendor->getErrors()) {
        $vendor->save();
    } else {
        return ['error' => 'Could not create vendor.'];
    }

    return ['success' => 'Y', 'vendorId' => $vendor->getId()];
  }// end function createVendor

	/**
	 * {@inheritdoc}
	 */
  public function getVendorOrders()
	{
    // load the seller by the customer ID
    $vendor = $this->vendor->loadByCustomerId($this->userContext->getUserId());

    // ced_csmarketplace_vendor_sales_order
    $ordersCollection = $vendor->getAssociatedOrders()->setOrder('id', 'DESC');
    
    // todo: format this data so that we get the order items
    $ordersCollection->from(
        array('order_items' => $resource->getTableName('sales_order_item')),
        array(
          'ordered_qty' => 'SUM(order_items.qty_ordered)',
          'order_item_name' => 'order_items.name',
          'order_item_total_sales' => 'SUM(order_items.row_total)',
          'sku'=>'order_items.sku'
        )
    )
    ->joinInner(
        array('order' => $resource->getTableName('sales_order')),
        implode(' AND ', $orderJoinCondition),
        array()
    );
    var_dump(get_class($ordersCollection));
    exit;
    return $ordersCollection->getData();
  }// end function getVendorOrders
}
