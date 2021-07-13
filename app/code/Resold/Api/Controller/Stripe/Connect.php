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
namespace Resold\Api\Controller\Stripe;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use Ced\CsMarketplace\Model\VendorFactory;

class Connect extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        VendorFactory $Vendor
    )
    {
        $this->session = $customerSession;
        $this->vendor = $Vendor;
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
      // Ensure user is logged in
      $sell_url = 'https://'.$_SERVER['HTTP_HOST'].'/sell';
      if (!$this->session->isLoggedIn()) {
        // return $resultRedirect->setPath('customer/account/login/referer/'.base64_encode($sell_url));
        return $resultRedirect->setPath('customer/account/create?referer='.urlencode($sell_url));
      }// end if user not logged in

      // check to make sure the user has authenticated with stripe
      // this means the vendor id should be non-null
      $vendorId = $this->session->getVendorId();

      // check if we need to create a seller account
      if($vendorId == null)
      {
          try {
          $vendorModel = $this->vendor->create();
          $customer = $this->session->getCustomer();
          $vendor = $vendorModel->setCustomer($customer)->register([
            'public_name' => $customer->getFirstname().' '.$customer->getLastname(),
            'shop_url' => uniqid()
          ]);
          $vendor->setGroup('general');
          if (!$vendor->getErrors()) {
              $vendor->save();
              $this->session->setVendorId($vendor->getId());
              $vendorId = $vendor->getId();
          } elseif ($vendor->getErrors()) {
              foreach ($vendor->getErrors() as $error) {
                  $this->session->addError($error);
              }
              $this->session->setFormData($vendor);
          } else {
              $this->session->addError(__('Your application has been denied'));
          }
        } catch (\Exception $e) { }// end try-catch creating a new vendor account
      }// end if vendor id not set

      $standalone = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
      $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

      if(count($stripe_model) !== 0){
        // check to see if connected to stripe
        return $resultRedirect->setPath($sell_url);
      }// end if user is already connected to stripe

      if(strpos($_SERVER['HTTP_HOST'], 'resold') !== FALSE){
        $stripe_client_id = '<your Stripe client ID>';
      }else{
        $stripe_client_id = '<your Stripe client ID>';
      }// end if on resold

      $customer = $this->session->getCustomer();
      $customer_name = $customer->getName();
      $name_parts = explode(' ', $customer_name);
      $prefill_fields = [
        'email' => $customer->getEmail(),
        'url' => 'https://resold.us',
        'product_description' => 'Resell goods to other users while using resold.us and to facilitate transactions involved on Resold.',
        'business_name' => $customer_name,
        'first_name' => $name_parts[0],
        'last_name' => $name_parts[1]
      ];

      $stripe_url = 'https://connect.stripe.com/express/oauth/authorize?response_type=code&client_id='.$stripe_client_id.'&scope=read_write&redirect_uri='.$sell_url;
      foreach($prefill_fields as $field => $value){
        $stripe_url .= "&stripe_user[".$field."]=".urlencode($value);
      }

      return $resultRedirect->setPath($stripe_url);
    }// end function execute
}
