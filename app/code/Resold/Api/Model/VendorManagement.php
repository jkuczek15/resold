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
    \Magento\Framework\App\ResourceConnection $resource,
    \Magento\Reports\Model\ResourceModel\Product\Sold\Collection $ordersCollection,
    \Magento\Catalog\Model\ResourceModel\Product $productResource,
    \Magento\Catalog\Model\Product\Type $productType 
  )
  {
      $this->userContext = $userContext;
      $this->vendorFactory = $vendorFactory;
      $this->vendor = $vendor;
      $this->resource = $resource;
      $this->customerRepository = $customerRepository;
      $this->ordersCollection = $ordersCollection;
      $this->productType = $productType;
      $this->productResource = $productResource;
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

    $compositeTypeIds = $this->productType->getCompositeTypes();
    $adapter = $this->resource->getConnection('read');
    $orderTableAliasName = $adapter->quoteIdentifier('order');

    $orderJoinCondition = [
      $orderTableAliasName . '.entity_id = order_items.order_id',
      $adapter->quoteInto("{$orderTableAliasName}.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
    ];

    $productJoinCondition = [
      $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
      'e.entity_id = order_items.product_id'
    ];

    $vendorOrdersSelect = $vendor->getAssociatedOrders()->getSelect()->reset()
    ->from(
        array('order_items' =>$this->resource->getTableName('sales_order_item')),
        array(
        'order.*',
        'product_id' => 'order_items.product_id',
        'product_name' => 'order_items.name',
        'product_price' => 'order_items.row_total',
        'product_sku' => 'order_items.sku'
      )
    )
    ->joinInner(
        array('order' => $this->resource->getTableName('sales_order')),
        implode(' AND ', $orderJoinCondition),
        array()
    )
    ->joinLeft(
        array('e' => $this->productResource->getEntityTable()),
        implode(' AND ', $productJoinCondition),
        array(
        'entity_id' => 'order_items.product_id',
        'type_id' => 'e.type_id',
      )
    )
    ->where('parent_item_id IS NULL')
    ->where('vendor_id="'.$vendor->getId().'"')
    ->order('order.entity_id DESC');

    $vendorOrders = $adapter->fetchAll($vendorOrdersSelect);

    return $vendorOrders;
  }// end function getVendorOrders

	/**
	 * {@inheritdoc}
	 */
  public function getStripeUrl()
	{
    // load the seller by the customer ID
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $vendor = $this->vendor->loadByCustomerId($this->userContext->getUserId());
    $vendorId = $vendor->getId();

    $standalone = $objectManager->create('Ced\CsStripePayment\Model\Standalone');
    $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

    if(count($stripe_model) === 0){
      // check to see if connected to stripe
      return 'https://resold.us/api/stripe/connect';
    }// end if user is already connected to stripe

    $stripe_id = $stripe_model['stripe_user_id'];

    // determine Stripe API mode
    $store = $objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' );
    $mode = $store->getValue ( 'payment/ced_csstripe_method_one/gateway_mode' );
    $skey = "api_{$mode}_secret_key";
    \Stripe\Stripe::setApiKey ( $store->getValue ( 'payment/ced_csstripe_method_one/' . $skey ) );

    $account = \Stripe\Account::retrieve($stripe_id);
    if($account['type'] == 'standard'){
      $dashboardLink = 'https://dashboard.stripe.com';
      return $dashboardLink;
    }else{
      $dashboardLink = $account->login_links->create();
      return $dashboardLink;
    }
  }// end function getVendorOrders
}
