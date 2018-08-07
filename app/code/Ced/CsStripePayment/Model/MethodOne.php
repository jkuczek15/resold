<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CsStripePayment
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStripePayment\Model;

use Magento\Framework\DataObject;
use Braintree\Exception;

/**
 * Pay In Store payment method model
 */
class MethodOne extends \Magento\Payment\Model\Method\AbstractMethod
{
	const METHOD_CODE = 'ced_csstripe_method_one';

	const PAYMENT_TYPE_MODE_TEST	= 'test';
	const PAYMENT_TYPE_MODE_LIVE	= 'live';


	protected $_isInitializeNeeded      = false;
	protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = true;
	protected $_canCapturePartial       = true;
	protected $_canRefund               = true;
	protected $_canVoid                 = true;
	protected $_canCancelInvoice        = true;
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
	protected $_canSaveCc               = false;
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'ced_csstripe_method_one';


    /**
     * @var string
     */
    protected $_formBlockType = 'Ced\CsStripePayment\Block\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Ced\CsStripePayment\Block\Info';





      public function _construct()
    {

    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	$store = $ob->get('Magento\Framework\App\Config\ScopeConfigInterface');
    	//$mode = $store->getConfig('payment/csstripepayment/mode');
    	$mode =  $store->getValue('payment/ced_csstripe_method_one/gateway_mode');

    	//$this->saveCards = $this->getConfigFlag('payment/csstripepayment/save_customer_card');
    	$path = "payment/ced_csstripe_method_one/api_{$mode}_secret_key";
    	$this->saveCards = $store->getValue('payment/ced_csstripe_method_one/save_customer_card');

    	$apiKey = $store->getValue($path);
    	\Stripe::setApiKey($apiKey);
    	\Stripe::setApiVersion('2013-10-29');

    	/* if($this->saveCards=='Disabled' || $this->saveCards==0 )
    	{
    		$this->registerCustomerOnStripe();
    	} */
    	if ($this->saveCards)
    	{

    		$this->registerCustomerOnStripe();     //Register Customer on Stripe Gateway
    	}

    }

    /*
     * Ensure Customer on Stripe Account
    */
    protected function registerCustomerOnStripe()
    {

    	$customerwithStripeId = $this->getCustomerwithStripeId();
        if(isset($this->customerLastRetrieved))
        {
        	$retrievedSecondsAgo = (time() - $this->customerLastRetrieved);
        }

    	if (!$customerwithStripeId)
    	{
    		$customer = $this->createCustomerOnStripe();
    	}
    	else if (isset($retrievedSecondsAgo) && ($retrievedSecondsAgo) > (60 * 10))
    	{


    		if (!$this->getStripeCustomer($customerwithStripeId))
    		{
    			$this->deleteStripeCustomerId($customerwithStripeId);
    			$this->createCustomerOnStripe();
    		}
    	}


    }






    protected function getCustomerwithStripeId($customerId = null){

    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	if (isset($this->customerStripeId))
    		return $this->customerStripeId;
    	// Get the magento customer id
    	if (empty($customerId))
    	{
    		$customerId = $ob->create('Magento\Customer\Model\Session')->getCustomer()->getId();

    	}


    	if (empty($customerId) || $customerId < 1)
    		return false;

    	$cusEmail =$ob->create('Magento\Customer\Model\Session')->getCustomer()->getEmail();

    	if ($cusEmail)
    	{
    		$result = $ob->create('Ced\CsStripePayment\Model\Customer')->load($cusEmail,'customer_email')->getData();

    	}


    	if (empty($result))
    	{
    		return false;
    	}
    	else{
    		$this->customerLastRetrieved = $result['last_retrieved'];

    		return $this->customerStripeId = $result['stripe_id'];
    	}


    }

    protected function createCustomerOnStripe(){
    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	$quote = $ob->create('Magento\Customer\Model\Session')->getCustomer();
    	$customername = $quote->getName();

    	$customerEmail = $quote->getEmail();
    	$customerId = $quote->getId();

    	if (empty($customerId) && empty($customerEmail))
    	{ return;}

    	// When we are in guest or new customer checkout, we may have already created this customer
    	if ($this->getCustomerStripeIdByEmail() !== false)
    	{ return;}
    	if (empty($customerId)){
    		$customerId = -1;
    	}

    	try{
    		$response = \Stripe_Customer::create(array(
    				"description" => $customername,
    				"email" => $customerEmail
    		));

    		$response->save();
    		$this->setStripeCustomerId($response->id, $customerId);
              die("gh;");
    		return $this->customer = $response;
    	}
    	catch (Exception $e){
    		Mage::helper('csstripepayment')->log('!customer profile could not set up!: '.$e->getMessage());
    		Mage::throwException($this->exception('!customer profile could not set up!: ').$this->exception($e->getMessage()));
    	}
    }


    protected function setStripeCustomerId($stripeId, $forCustomerId)
    {
    	try
    	{
    		$ob =  \Magento\Framework\App\ObjectManager::getInstance();

    		$fields = array();
    		$fields['stripe_id'] = $stripeId;
    		$fields['customer_id'] = $forCustomerId;
    		$fields['last_retrieved'] = time();
    		$fields['customer_email'] = $ob->create('Magento\Customer\Model\Session')->getCustomer()->getEmail();
    		$custStripeData = $ob->create('Ced\CsStripePayment\Model\Customer');
    		$custStripeData->setData('customer_id',$forCustomerId);
    		$custStripeData->setData('stripe_id',$stripeId);
    		$custStripeData->setData('last_retrieved',time());
    		$custStripeData->setData('customer_email',$ob->create('Magento\Customer\Model\Session')->getCustomer()->getEmail());
    		$custStripeData->save();
    		return true;
    	}
    	catch (Exception $e)
    	{
    		Mage::helper('csstripepayment')->log($this->exception(' Stripe customers table Unable to Update: '.$e->getMessage()));
    	}
    }



    public function getStripeCustomer($id = null)
    {
    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	if (isset($this->customer))
    	{
    		return $this->customer;
    	}
    	if (empty($id))
    	{
    		$id = $this->getCustomerwithStripeId();
    	}

    	try
    	{
    		$this->customer = \Stripe_Customer::retrieve($id);

    		$model = $ob->create('Ced\CsStripePayment\Model\Customer')->load($this->customer->id,'stripe_id');
    		$id = $model->getId();
    		$stripeModel = $ob->create('Ced\CsStripePayment\Model\Customer')->load($id);
    		$stripeModel->setData('last_retrieved',time());
    		$stripeModel->save();

    		if (!$this->customer || $this->customer->deleted)
    			return false;
    		return $this->customer;
    	}
    	catch (Exception $e)
    	{
    		$ob->create('Ced\CsStripePayment\Helper\Data')->log($this->exception('Could not retrieve customer profile: '.$e->getMessage()));
    		return false;
    	}
    }
    protected function getCustomerStripeIdByEmail($maxAge = null)
    {
    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	$email = $ob->create('Magento\Customer\Model\Session')->getCustomer()->getEmail();

    	if (empty($email))
    		return false;


    	/* if (!empty($maxAge))
    		$query = $query->where('last_retrieved >= ?', time() - $maxAge); */
    	$result = $ob->create('Ced\CsStripePayment\Model\Customer')->load($email,'customer_email')->getData();
    	if (empty($result))
    	{
    		return false;
    	}
    	else{
    		return $this->customerStripeId = $result['stripe_id'];
    	}

    }
    protected function deleteStripeCustomerId($stripeId)
    {
    	try
    	{
    		$scopeConfig = $ob->create('\Magento\Framework\App\ResourceConnection');
    		$connection =$scopeConfig->getConnection('default');
    		$condition = array($connection->quoteInto('stripe_id=?', $stripeId));
    		$connection->delete('cryozonic_stripesubscriptions_customers',$condition);
    	}
    	catch (Exception $e)
    	{
    		Mage::helper('csstripepayment')->log($this->exception('Could not clear Stripe customers table: '.$e->getMessage()));
    	}
    }

    /*
     * assign Customer Payment data
    */

    public function assignData(DataObject $data)
    {

    	 if (!($data instanceof DataObject)) {
                $data = new DataObject($data);
            }
        //    print_r($data);die("k");
            $infoinstance = $this->getInfoInstance();




            if (!empty($data['additional_data']['cc_saved']) && $data['additional_data']['cc_saved'] != 'new_card'){

            	$infoinstance->setAdditionalInformation('token', $data['additional_data']['cc_saved']);
            	return $this;
            }
            if (empty($data['additional_data']['cc_stripejs_token']) && empty($data['additional_data']['cc_number'])){

            	return $this;}

            	if (!empty($data['additional_data']['cc_stripejs_token'])){

            		/*called at Card Filling Step*/
            		$usedToken = $infoinstance->getAdditionalInformation('stripejs_token');
            		if (!empty($usedToken) && $usedToken == $data['additional_data']['cc_stripejs_token'])
            		{
            			return $this;
            		}


            		$params = array(
            				"card" => $data['additional_data']['cc_stripejs_token']
            		);
            		$infoinstance->setAdditionalInformation('stripejs_token',$data['additional_data']['cc_stripejs_token']);
            	}
            	else{

            		$params = array(
            				"card" => array(
            					//	"name" => $data['additional_data']['cc_owner'],
            						"number" => $data['additional_data']['cc_number'],
            						"cvc" => $data['additional_data']['cc_cid'],
            						"exp_month" => $data['additional_data']['cc_exp_month'],
            						"exp_year" => $data['additional_data']['cc_exp_year']
            				)
            		);
            	}
            	/** Add the card to the customer */
            	if ($this->saveCards && $data['cc_save']){

            		$cu = $this->getStripeCustomer($this->getCustomerwithStripeId());

            		if ($cu)
            		{
            			try
            			{
            				$card = $this->addCardToStripeCustomer($cu, $params['card']);
            				$token = $card->id;

            			}
            			catch (Exception $e)
            			{
            				die("fff");
            				$token = $this->createToken($params);
            			}
            		}
            	}
            	else{
            		try{
            			$token = $this->createToken($params);

            		}
            		catch(\Exception $e)
            		{
            			echo $e; die("hlkn");
            		}

            	}
            		$infoinstance->setAdditionalInformation('token', $token);

            		//->setCcOwner($data['additional_data']['cc_owner'])
            		$infoinstance->setCcType($data['additional_data']['cc_type'])
            		->setCcExpMonth($data['additional_data']['cc_exp_month'])
            		->setCcExpYear($data['additional_data']['cc_exp_year'])
            		->setCcNumber($data['additional_data']['cc_number'])
            		->setCcNumberEnc($data['additional_data']['cc_number'])
            		->setCcLast4('************'.substr($data['additional_data']['cc_number'],-4,4))
            		->setCcCid($data['additional_data']['cc_cid']);
            		return $this;




    }

    public function assignDataaxzaxax(DataObject $data)
    {

    	if (!($data instanceof DataObject)) {
    		$data = new DataObject($data);
    	}
    	$Cardinfo = $this->getInfoInstance();
    	$Cardinfo->setCcType($data['additional_data']['cc_type'])
    	->setCcLast4(substr($data['additional_data']['cc_number'], -4))
    	->setCcNumber($data['additional_data']['cc_number'])
    	//->setCcCid($data['additional_data']['cc_cid'])
    	->setCcExpMonth($data['additional_data']['cc_exp_month'])
    	->setCcExpYear($data['additional_data']['cc_exp_year']);
    	return $this;
    }


    protected function createToken($params)
    {

    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	// If the card is already a token, such as from Stripe.js, then don't create a new token
    	if (is_string($params['card']) && strpos($params['card'], 'tok_') === 0)
    	{ return $params['card'];}

    	try
    	{
    		$params['card'] = $this->getAvsFields($params['card']);
    		$token = \Stripe_Token::create($params);

    		if (empty($token['id']) || strpos($token['id'],'tok_') !== 0)
    			$ob->create('\Magento\Framework\Message\ManagerInterface')->addError('Sorry, this payment method can not be used at the moment. Try again later.');

    		return $token['id'];
    	}
    	catch (\Stripe_CardError $e)
    	{
    		$ob->create('\Magento\Framework\Message\ManagerInterface')->addError($e->getMessage());
    	}
    }

    protected function getAvsFields($card)
    {
    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();

    	if (!is_array($card)) return $card; // Card is a token so AVS should have already been taken care of

    	/* if (Mage::getStoreConfig('payment/csstripe/avs'))
    	{ */
    		$checkout =$ob->create('Magento\Checkout\Model\Session')->getQuote();
    		$billAddress = $checkout->getBillingAddress();
    		$card['address_line1'] = $billAddress->getData('street');
    		$card['address_zip'] = $billAddress->getData('postcode');

    		// If there is no checkout session then we must be coming here from the back office.
    		if (empty($card['address_line1'])) {
    			$quote = $this->getInfoInstance()->getQuote();

    			if (!empty($quote)) {
    				$billAddress = $quote->getBillingAddress();
    				$card['address_line1'] = $billAddress->getData('street');
    				$card['address_zip'] = $billAddress->getData('postcode');
    			}
    		}
    	/* } */
    	return $card;
    }






    /**
     * Send authorize request to gateway
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

    	parent::authorize($payment, $amount);
       if ($amount > 0)
		{

			$this->generateCharge($payment, $amount, false);
		}
		return $this;
    }



    public function generateCharge($payment, $amount, $capture, $forceUseSavedCard = false)
    {

    	$ob =  \Magento\Framework\App\ObjectManager::getInstance();
    	if ($forceUseSavedCard)
    	{
    		$token = $this->getSavedCardFrom($payment);
    		$this->customerStripeId = $this->getCustomerwithStripeId($payment->getOrder()->getCustomerId());

    		if (!$token || !$this->customerStripeId)
    			$ob->create('\Magento\Framework\Message\ManagerInterface')->addError('The authorization has expired and the customer has no saved cards to re-create the order.');
    	}
    	else{
    		$token = $this->getcreatedToken();
    	}
    	try {

    		$order = $payment->getOrder();
    	/* 	if (Mage::getStoreConfig('payment/csstripepayment/use_store_currency'))
    		{
    			$amount = $order->getGrandTotal();
    			$currency = $order->getOrderCurrencyCode();
    		}
    		else
    		{ */
    			$amount = $order->getBaseGrandTotal();
    			$subtotal=$order->getSubtotal();
    			$shippingamount=$order->getShippingAmount();
    			$currency = $order->getBaseCurrencyCode();
    			$shippingmethod=$order->getShippingDescription();
    	/* 	} */


    		$cents = 100;
    		/* if ($ob->create('Ced\CsStripePayment\Helper\Data')->isZeroDecimal($currency))
    			$cents = 1; */


    		$params = array(
    				"amount" => round($amount * $cents),
    				"currency" => $currency,
    				"card" => $token,
    				"description" => "Order #".$order->getRealOrderId().' by '.$order->getCustomerName(),
    				"capture" => $capture,
    				"metadata" => array("Subtotal"=>$subtotal,"shipping Tax Rate" =>$shippingamount,"Grand Total"=>$amount)
    		);


    		/**pass the customer Id if this id saved card*/
    		if(strpos($token,'card_') === 0)
    		{
    			$params["customer"] = $this->getCustomerwithStripeId($order->getCustomerId());
    		}
    		try
    		{
    			$charge = \Stripe_Charge::create($params);

;    		}
    		catch (Exception $e)
    		{
    			$ob->create('\Magento\Framework\Message\ManagerInterface')->addError($e->getMessage());
    		}
    		$payment->setTransactionId($charge->id);
    		$payment->setAdditionalInformation('captured', $capture);
    		$payment->setIsTransactionClosed(0);

    		// Set the order status according to the configuration
    		$newOrderStatus = Mage::getStoreConfig('payment/csstripepayment/order_status');
    		if (!empty($newOrderStatus))
    		{
    			$order->addStatusToHistory($newOrderStatus, $this->t('Changing order status as per New Order Status configuration'));
    		}
    	}
    	catch(Stripe_CardError $e)
    	{
    		$ob->create('\Magento\Framework\Message\ManagerInterface')->addError($e->getMessage());
    	}
    }

    protected function getcreatedToken()
    {
    	$info = $this->getInfoInstance();
    	$token = $info->getAdditionalInformation('token');

    	// Is this a saved card?
    	if (strpos($token,'card_') === 0)
    		return $token;
    	if (strstr($token,'tok_') === false)
    	{
    		$params = $this->getInfoInstanceCard();
    		$token = $this->createToken($params);
    	}
    	return $token;
    }

    protected function getSavedCardFrom(Varien_Object $payment)
    {
    	$card = $payment->getAdditionalInformation('token');

    	if (strstr($card, 'card_') === false)
    	{
    		// $cards will be NULL if the customer has no cards
    		$cards = $this->getCustomerCards(true, $payment->getOrder()->getCustomerId());
    		if (is_array($cards) && !empty($cards[0]))
    			return $cards[0]->id;
    	}

    	if (strstr($card, 'card_') === false)
    		return null;
    	return $card;
    }


    public function getCustomerCards($isAdmin = false, $customerId = null)
    {
    	if (!$this->saveCards && !$isAdmin)
    		return null;

    	if (!$customerId)
    		$customerId = Mage::helper('csstripepayment')->getCustomerId();

    	if (!$customerId)
    		return null;

    	$customerwithStripeId = $this->getCustomerwithStripeId($customerId);
    	if (!$customerwithStripeId)
    		return null;
    	if (!$isAdmin && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0') !== false)
    		return null;

    	return $this->HasCards($customerwithStripeId);
    }












    /**
     * @see Mage_Payment_Model_Method_Abstract::capture()
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount){die("lkcdcd");
    	$order = $payment->getOrder();
        if($amount>0){
    		$response = $this->callStripeApi($payment,$amount);
    		if($response === false) {
    			$errorMsg = __('Invalid Data Restrict Process');
    		} else {
    			if($response['status'] == 1){
    				$payment->setTransactionId($response['transaction_id']);
    				$payment->setIsTransactionClosed(1);
    			}else{
    				Mage::throwException(__('Occurred Error In Processing'));
    			}
    		}
    		return $this;
		}
    }

    /**
     *
     * @param Varien_Object $payment
     * @param unknown $amount
     * @return multitype:number NULL
     */
    private function callStripeApi(\Magento\Payment\Model\InfoInterface $payment, $amount){
    	$order = $payment->getOrder();
    	$billingaddress = $order->getBillingAddress();
    	$orderId = $order->getIncrementId();
    	$currencycode = $order->getBaseCurrencyCode();

    	$totals = number_format($amount, 2, '.', '');

    	$mode = $this->getConfigData('gateway_mode');

    	/**Get API mode test/live */
    	$skey="api_{$mode}_secret_key";

    	\Stripe::setApiKey($this->getConfigData($skey));
    	try
    	{
    		$customerFirstname=$billingaddress->getData('firstname');
    		$customerLastname=$billingaddress->getData('lastname');
    		$cents=100;

    		/**CREATE CUSTOMER */
    		$response = \Stripe_Customer::create(array(
    				"description" => "$customerFirstname $customerLastname",
    				"email" => $order->getCustomerEmail()
    		));

    		$Tokenparams = array(
    				"card" => array(
    						"name" =>$billingaddress->getData('firstname').' '.$billingaddress->getData('lastname'),
    						"number" => $payment->getCcNumber(),
    						"cvc" => $payment->getCcCid(),
    						"exp_month" =>$payment->getCcExpMonth(),
    						"exp_year" =>$payment->getCcExpYear(),
    				)
    		);

    		$Tokenparams['card']=$this->getBillingAddress($Tokenparams['card'],$billingaddress);

    		/**CREATE TOKEN */
    		$createtoken= \Stripe_Token::create($Tokenparams);
    		$Chargeparams = array(
    				"amount" => $totals*$cents,
    				"currency" => $currencycode,
    				"source" => $createtoken->id,
    				"description" => sprintf('there is an order #%s with customer email %s', $orderId,$response->email)
    		);

    		/**CREATE CHARGE */
    		$createcharge=\Stripe_Charge::create($Chargeparams);
    		return array('status'=>1,'transaction_id' => $createcharge->id,'fraud' => rand(0,1));
    	}
    	catch (Exception $e) {
    		Mage::throwException(Mage::helper('stripe')->__($e->getMessage()));
    	}
    }

    /**BILLING ADDRESS ARRAY */
    public function getBillingAddress($Token,$billingaddress){
    	$Token['address_line1']=$billingaddress->getData('street');
    	$Token['address_city']=$billingaddress->getData('city');
    	$Token['address_state']=$billingaddress->getData('region');
    	$Token['address_zip']=$billingaddress->getData('postcode');
    	$Token['address_country']=$billingaddress->getData('country_id');
    	return $Token;
    }
}
