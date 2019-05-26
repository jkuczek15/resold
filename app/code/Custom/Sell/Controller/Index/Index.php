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
namespace Custom\Sell\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Ced\CsMarketplace\Helper\Data;
use Magento\Framework\Module\Manager;
use Ced\CsMarketplace\Model\VendorFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        Manager $moduleManager,
        VendorFactory $Vendor,
        Data $datahelper,
        \Magento\Catalog\Block\Product\Context $productContext
    )
    {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->vendor = $Vendor;
        $this->helper = $datahelper;
        $this->_coreRegistry = $productContext->getRegistry();
        $this->_storeManager = $productContext->getStoreManager();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->session->isLoggedIn()) {
            $_SESSION['from_sell_form'] = true;
            $sell_url = 'https://'.$_SERVER['HTTP_HOST'].'/sell';
            return $resultRedirect->setPath('customer/account/create?referer='.urlencode($sell_url));
        }// end if the user is not logged in

        // check to make sure the user has authenticated with stripe
        // this means the vendor id should be non-null
        $vendorId = $this->session->getVendorId();
        $standalone = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
        $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

        // check for a stripe authentication code
        if(isset($_GET['code']) && count($stripe_model) == 0){
          // stripe authentication code was passed, authenticate the user
          $this->stripeAuth($_GET['code']);

          // enable the user's products
          $vendor = $this->_coreRegistry->registry('current_vendor');
          if($vendor != null){
            $vendorId = $vendor->getId();
          }else{
            $vendorId = $this->session->getVendorId();
          }

          $collection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId == null ? -1 : $vendorId);
          $products = [];
          foreach ($collection as $productData) {
              array_push($products, $productData->getProductId());
          }// end foreach over product data

          // enable the user's products
          $cedProductcollection = $this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                ->addAttributeToSelect($this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes())
                ->addAttributeToFilter('entity_id', ['in' => $products])
                ->addStoreFilter($this->getCurrentStoreId())
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
                ->addAttributeToSort('date', 'desc');

          foreach($cedProductcollection as $product)
          {
            $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $product->save();
          }// end foreach loop updating products to enabled

          // redirect the listing page
          $this->messageManager->addSuccess('Your items are now live for sale on Resold.');
          return $resultRedirect->setPath('customer/account/listings');
        }// end if stripe authentication code was passed and the user is not signed up with stripe

        // GET request
        return $this->resultPageFactory->create();
    }

    public function stripeAuth($code)
    {
      $ob = $this->_objectManager;
      $store = $ob->get('Magento\Framework\App\Config\ScopeConfigInterface');

      if($store->getValue('payment/ced_csstripe_method_one/account_type')=='standalone'){
        // standard stripe accounts
        $TOKEN_URI = 'https://connect.stripe.com/oauth/token';
        $clientIdMode = $store->getValue('payment/ced_csstripe_method_one/client_id_mode');

        if($clientIdMode=='Development'){
          $clientId = $store->getValue('payment/ced_csstripe_method_one/client_did');
        }else{
          $clientId = $store->getValue('payment/ced_csstripe_method_one/client_pid');
        }

        $mode = $store->getValue('payment/ced_csstripe_method_one/gateway_mode');
        /** get API mode test/live */
        $skey = "api_{$mode}_secret_key";

        try {
          $key = $store->getValue('payment/ced_csstripe_method_one/'.$skey);

          $token_request_body = array(
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'code' => $code,
            'client_secret' =>$key
          );

          /*
           Array ( [access_token] => sk_test_6eJ53weZmD6wdefcJDnfP4Dg [livemode] => [refresh_token] => rt_8VOzR1nDhrSpfBM8LPYrFRjiM2Wmeseyg49ZwP04xsjtmdjb [token_type] => bearer [stripe_publishable_key] => pk_test_yCEEPiKjXyOtIuHRnbE8lz6F [stripe_user_id] => acct_188F18Iw2Ylw9dxv [scope] => read_write ) sk_test_6eJ53weZmD6wdefcJDnfP4Dgdfsf
           */
          // authorization request to stripe
          $req = curl_init('https://connect.stripe.com/oauth/token');
          curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($req, CURLOPT_POST, true );
          curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

          $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
          $resp = json_decode(curl_exec($req), true);
          curl_close($req);

          $vendorId = $this->session->getVendorId();
          $model = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
          $model1 = $model->load($vendorId, 'vendor_id')->getData();

          if(count($model1) > 0){
            $data = array('access_token'=>$resp['access_token'],'refresh_token'=>$resp['refresh_token'],'token_type'=>$resp['token_type'],
            'stripe_publishable_key'=>$resp['stripe_publishable_key'],'stripe_user_id'=>$resp['stripe_user_id'],
            'scope'=>$resp['scope']);


            $id = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone')->load($vendorId,'vendor_id')->getId();
            $model = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone')->load($id);
            try {
              $model->setData('access_token',$resp['access_token'])
              ->setData('refresh_token',$resp['refresh_token'])
              ->setData('token_type',$resp['token_type'])
              ->setData('stripe_publishable_key',$resp['stripe_publishable_key'])
              ->setData('stripe_user_id',$resp['stripe_user_id'])
              ->setData('scope',$resp['scope'])
              ->setData('vendor_id',$vendorId)
              ->save();

              return;
            } catch (\Exception $e){
              echo $e->getMessage();
            }

          }else{
            // save the stripe standalone API data
            $model->setData('access_token',$resp['access_token'])
                ->setData('refresh_token',$resp['refresh_token'])
                ->setData('token_type',$resp['token_type'])
                ->setData('stripe_publishable_key',$resp['stripe_publishable_key'])
                ->setData('stripe_user_id',$resp['stripe_user_id'])
                ->setData('scope',$resp['scope'])
                ->setData('vendor_id', $vendorId)
                ->save();

            return;
          }
        }
        catch(\Exception $e){
          $this->_objectManager->create('Magento\Customer\Model\Session')->addError($e->getMessage());
          $this->_redirect('index');
          return;
        }
      } else if (isset($_GET['error'])) { // Error
        echo $_GET['error_description'];
      } else {
        $authorize_request_body = array(
            'response_type' => 'code',
            'scope' => 'read_write',
            'client_id' => $clientId
        );
      }
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    protected function _getSession()
    {
    	return $this->_objectManager->create('Magento\Customer\Model\Session');
    }
}
