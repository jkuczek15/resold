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
    \Ced\CsMarketplace\Model\VendorFactory $Vendor,
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
  )
  {
      $this->userContext = $userContext;
      $this->vendor = $Vendor;
      $this->customerRepository = $customerRepository;
  }

	/**
	 * {@inheritdoc}
	 */
  public function createVendor()
	{
    $customerId = $this->userContext->getUserId();

    // create a mew vendor/seller account
    $vendorModel = $this->vendor->create();

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
	}
}
