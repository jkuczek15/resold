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
namespace Custom\Api\Controller\Stripe;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;

class Dashboard extends \Magento\Framework\App\Action\Action
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
        Session $customerSession
    )
    {
        $this->session = $customerSession;
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
      $standalone = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
      $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

      if(count($stripe_model) === 0){
        // check to see if connected to stripe
        return $resultRedirect->setPath($sell_url);
      }// end if user is already connected to stripe

      $stripe_id = $stripe_model['stripe_user_id'];

      // determine Stripe API mode
			$store = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' );
  		$mode = $store->getValue ( 'payment/ced_csstripe_method_one/gateway_mode' );
  		$skey = "api_{$mode}_secret_key";
  		\Stripe\Stripe::setApiKey ( $store->getValue ( 'payment/ced_csstripe_method_one/' . $skey ) );

      $account = \Stripe\Account::retrieve($stripe_id);
      $dashboard_link = $account->login_links->create();
      return $resultRedirect->setPath($dashboard_link->url);
    }// end function execute
}
