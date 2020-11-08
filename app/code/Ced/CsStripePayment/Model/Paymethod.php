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
class Paymethod extends \Magento\Payment\Model\Method\AbstractMethod {
	const METHOD_CODE = 'stripe';
	const PAYMENT_TYPE_MODE_TEST = 'test';
	const PAYMENT_TYPE_MODE_LIVE = 'live';
	const PRODUCTION = 'production';
	const DEVELOPMENT = 'development';
	const MANAGED = 'managed';
	const STANDALONE = 'standalone';
	protected $_canAuthorize = true;
	protected $_canCapture = true;
	/**
	 * Payment code
	 *
	 * @var string
	 */
	protected $_code = 'stripe';
	protected $_objectManager;
	/**
	 *
	 * @var string
	 */
	protected $_formBlockType = 'Ced\CsStripePayment\Block\Form';

	/**
	 *
	 * @var string
	 */
	protected $_infoBlockType = 'Ced\CsStripePayment\Block\Info';
	public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Payment\Helper\Data $paymentData, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Payment\Model\Method\Logger $logger1, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\ObjectManagerInterface $objectInterface, array $data = []) {
		parent::__construct ( $context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger1, null, null, $data );
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_objectManager = $objectInterface;
	}
	public function assignData(DataObject $data) {
		// $a = var_export($data);
		// throw new \Magento\Framework\Exception\LocalizedException ( __ ( $a ) );
		if (! ($data instanceof DataObject)) {
			$data = new DataObject ( $data );
		}
		$Cardinfo = $this->getInfoInstance ();
		if (!empty($data['additional_data']['cc_saved']) && $data['additional_data']['cc_saved'] != 'new_card'){
			$Cardinfo->setAdditionalInformation('token', $data['additional_data']['cc_saved']);
			return $this;
		}
		$Cardinfo->setCcType ( $data ['additional_data'] ['cc_type'] )->setCcLast4 ( substr ( $data ['additional_data'] ['cc_number'], - 4 ) )->setCcNumber ( $data ['additional_data'] ['cc_number'] )->setCcCid ( $data ['additional_data'] ['cc_cid'] )->setCcExpMonth ( $data ['additional_data'] ['cc_exp_month'] )->setCcExpYear ( $data ['additional_data'] ['cc_exp_year'] );
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Magento\Payment\Model\Method\AbstractMethod::authorize()
	 */
	public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount) {
		$payment->setShouldCloseParentTransaction ( false );
		$payment->setAdditionalInformation ( 'cc_number', $payment->getCcNumber () );
		$payment->setAdditionalInformation ( 'cvc', $payment->getCcCid () );
		$payment->setAdditionalInformation ( 'exp_month', $payment->getCcExpMonth() );
		$payment->setAdditionalInformation ( 'exp_year', $payment->getCcExpYear() );
	    //print_r($payment->getAdditionalInformation()); die("fkj");
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see \Magento\Payment\Model\Method\AbstractMethod::capture()
	 */
	public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {
		if ($this->_scopeConfig->getValue ( 'payment/stripe/payment_action', \Magento\Store\Model\ScopeInterface::SCOPE_STORE ) != 'authorize') {
			$order = $payment->getOrder ();
			$response = $this->callStripeApi ( $payment, $amount );
			if ($response === false) {
				$errorMsg = __ ( 'Invalid Data Restrict Process' );
			} else {
				if ($response ['status'] == 1) {
					// $payment->setTransactionId($response['transaction_id']);
					$payment->setIsTransactionClosed ( 1 );
				} else {
					throw new \Magento\Framework\Exception\LocalizedException ( __ ( 'Occurred Error In Processing' ) );
				}
			}
			return $this;
		} else {
			$order = $payment->getOrder ();
			if ($amount > 0) {
				$response = $this->createCharge ( $payment, $amount );
				if ($response === false) {
					$errorMsg = $this->_getHelper ()->__ ( 'Invalid Data Restrict Process' );
				} else {
					if ($response ['status'] == 1) {
						$payment->setTransactionId ( $response ['transaction_id'] );
						// $payment->setIsTransactionClosed(1);
						$payment->setIsTransactionClosed ( 0 );

						$payment->setShouldCloseParentTransaction ( false );
					} else {
						throw new \Magento\Framework\Exception\LocalizedException ( __ ( 'Occurred Error In Processing' ) );
					}
				}

				return $this;
			}
		}
	}
	private function callStripeApi(\Magento\Payment\Model\InfoInterface $payment, $amount) {
		try {

			return array (
					'status' => 1,
					'fraud' => rand ( 0, 1 )
			);
		} catch ( \Exception $e ) {
			throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
		}
	}
	public function createCharge(\Magento\Payment\Model\InfoInterface $payment, $amount) {
		try {

			$currencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ( null )->getBaseCurrencyCode ();
			$country = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'general/country/default' );

			$order = $payment->getOrder ();
			$orderID = $order->getIncrementId ();
		    $billingaddress = $order->getBillingAddress ();


			$products = $order->getItemsCollection ()->addFieldToSelect ( '*' );

			$products = $order->getAllItems();
			$baseToGlobalRate = $order->getBaseToGlobalRate () ? $order->getBaseToGlobalRate () : 1;
			$vendorsBaseOrder = array ();
			$vendorQty = array();
			foreach ( $products as $key => $item ) {
				if ($vendor_id = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($item->getProductId())) {
					$price = 0;

					$price = $item->getBaseRowTotal () + $item->getBaseTaxAmount () + $item->getBaseHiddenTaxAmount () + $item->getBaseWeeeTaxAppliedRowAmount () - $item->getBaseDiscountAmount ();
					$vendorsBaseOrder [$vendor_id] ['order_total'] = isset ( $vendorsBaseOrder [$vendor_id] ['order_total'] ) ? ($vendorsBaseOrder [$vendor_id] ['order_total'] + $price) : $price;
					$vendorsBaseOrder [$vendor_id] ['item_commission'] [$item->getQuoteItemId ()] = $price;

					$vendorsBaseOrder [$vendor_id] ['order_items'] [] = $item;
					$vendorQty [$vendor_id] = isset ( $vendorQty [$vendor_id] ) ? $vendorQty [$vendor_id] + $item->getQty () : $item->getQtyOrdered ();

					$logData = $item->getData ();
					$item->setVendorId ( $vendor_id );

					unset ( $logData ['product'] );
				}
			}
		} catch ( \Exception $e ) {
			throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
		}

		$count = 0;
		foreach ( $vendorsBaseOrder as $vendorId => $baseOrderTotal ) {
			$count ++;
			if ($count = 1) {
				$shippingAmount = $order->getShippingAmount();
			} else {
				$shippingAmount = 0;
			}

			try {

				$qty = isset ( $vendorQty [$vendorId] ) ? $vendorQty [$vendorId] : 0;
				$vorder = $this->_objectManager->create('Ced\CsMarketplace\Model\Vorders')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)
				          ->addFieldToFilter('order_id',$orderID)->getFirstItem();

				$billingadd = $order->getBillingAddress()->getData();
				if (isset ( $billingaddress ['middlename'] )) {
					$billing_name = $billingadd ['firstname'] . " " . $billingadd ['middlename'] . " " . $billingadd ['lastname'];
				} else {
					$billing_name = $billingadd ['firstname'] . " " . $billingadd ['lastname'];
				}

				// ** stripe code starts
				$store = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' );
				if ($payment->getMethod () == 'stripe') {


					// echo $paytovendor; echo "<br>"; echo $paytoadmin; die("fl");

					// echo $amount; echo "<br>";echo $fee; echo "<br>"; die("lg");
					try {

						$mode = $store->getValue ('payment/stripe/gateway_mode' );
						$appFee = $store->getValue ('payment/stripe/app_fee' );
						$skey = "api_{$mode}_secret_key";

						\Stripe\Stripe::setApiKey ( $store->getValue ( 'payment/stripe/' . $skey ) );

						if ($payment->getMethod () == 'stripe') {

							$vendorSetting = $this->_objectManager->create ( 'Ced\CsMarketplace\Model\Vsettings' )->getCollection ()->addFieldToFilter ( 'vendor_id', $vendorId )->addFieldToFilter ( 'group', "payment" )->getData ();
							$vactive = 0;
							$vemail = null;
							$vaccNu = null;
							$routingNumber = null;
							foreach ( $vendorSetting as $key => $value ) {
								if ($value ['key'] == "payment/vstripe/active") {
									$vactive = $value ['value'];
								}
								if ($value ['key'] == "payment/vstripe/stripe_email") {
									$vemail = $value ['value'];
								}
								if ($value ['key'] == "payment/vstripe/account_number") {
									$vaccNu = $value ['value'];
								}
								if ($value ['key'] == "payment/vstripe/routing_number") {
									$routingNumber = $value ['value'];
								}
							}

							if ($vactive){

								if ($store->getValue ('payment/stripe/account_type' ) == 'managed') {
									$check_acc = $this->_objectManager->create ( 'Ced\CsStripePayment\Model\Managed' )->getCollection ()->addFieldToFilter ( 'vendor_id', $vendorId )->addFieldToFilter ( 'email_id', $vemail )->getData ();

									if ($check_acc != null) {
										$stripe_acc = $check_acc [0] ['account_id'];

									}

									/**
									 * when customer initiates stripe account
									 */
									else {

										if ($store->getValue ( 'payment/stripe/account_type' ) == 'managed') {
											try {
												$account = \Stripe\Account::create ( array (
														"managed" => true,
														"country" => $country,
														"email" => $vemail,
														"dob" => [
																"day" => null,
																"month" => null,
																"year" => null
														],
														"external_account" => [
																"object" => "bank_account",
																"account_number" => $vaccNu,
																"country" => $country,
																"currency" => $currencyCode,
																"routing_number" => $routingNumber
														]
												) );
											} catch ( \Exception $e ) {

												$this->_objectManager->get ( 'Magento\Framework\Exception\PaymentException' )->throwException ( __ ( $e->getMessage () ) );
											}

											try {

												$custStripeData = $this->_objectManager->create ( 'Ced\CsStripePayment\Model\Managed' );
												$custStripeData->setData ( 'vendor_id', $vendorId );
												$custStripeData->setData ( 'account_id', $account->id );
												$custStripeData->setData ( 'email_id', $vemail );
												$custStripeData->setData ( 'secret_key', $account->keys->secret );
												$custStripeData->setData ( 'publishable_key', $account->keys->publishable );
												$custStripeData->save ();
											} catch ( \Exception $e ) {
												throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
											}

											$stripe_acc = $account->id;
										}
									}
								}
								else {

									$getData = $this->_objectManager->create ( 'Ced\CsStripePayment\Model\Standalone' )->getCollection ()->getData ();

									/**
									 * get token, if Mode is set to standalone
									 */
									foreach ( $getData as $get ) {
										if ($vendorId == $get ['vendor_id']) {
											$stripe_account = $get ['stripe_user_id'];
											$token = $get ['access_token'];
										}
									}

									$stripe_acc = $stripe_account;
									// print_r($stripe_acc); die("h");
								}
							}
							// die("rf");
							$addInfo = $payment->getAdditionalInformation();
							$addInfo['exp_month'] =

							$Tokenparams = array (
									"card" => array (
											"name" => $billingaddress->getData ( 'firstname' ) . ' ' . $billingaddress->getData ( 'lastname' ),
											"number" => $addInfo['cc_number'],
											"cvc" => $addInfo['cvc'],
											"exp_month" => $addInfo['exp_month'],
											"exp_year" => $addInfo['exp_year']
									)
							);

							$Tokenparams ['card'] = $this->getBillingAddress ( $Tokenparams ['card'], $billingaddress );

							if ($vactive) {

								$amount = $baseOrderTotal ['order_total'];

								$commissionFee = $vorder->getShopCommissionFee ();

								$vshipping = $vorder->getShippingAmount ();
								$shippingtoVendor = $vshipping;
								$shippingtoAdmin = $shippingAmount - $shippingtoVendor;
								$paytovendor = $amount - $commissionFee + $shippingtoVendor;

								$paytoadmin = $commissionFee + $shippingtoAdmin;
								// echo $paytovendor;echo "<br>";echo $paytoadmin; die("lkh");
								$createtoken1 = \Stripe\Token::create ( $Tokenparams );
								$createtoken2 = \Stripe\Token::create ( $Tokenparams );
								try {
									$charge1 = \Stripe\Charge::create ( array (
											"amount" => $paytovendor * 100, // amount in cents
											"currency" => $currencyCode,
											"source" => $createtoken1->id,
											"description" => "Create Payment",
											"application_fee" => $appFee
									)
									, array (
											"stripe_account" => $stripe_acc
									) );
								} catch ( \Stripe\Error\Card $e ) {
									throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
								}

								if ($paytoadmin > 0) {
									try {
										$charge2 = \Stripe\Charge::create ( array (
												"amount" => $paytoadmin * 100, // Amount in cents
												"currency" => $currencyCode,
												"source" => $createtoken2->id,
												"description" => "Create Payment",
												"application_fee" => $appFee
										// amount in cent
																				) );
									} catch ( \Stripe\Error\Card $e ) {

										throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
									}
								}

								if (isset ( $charge2 )) {
									$transacionId = $charge1->id . ',' . $charge1->id;
								} else {
									$transacionId = $charge1->id;
								}
								$status = 2;
								$totalAmount = $paytovendor + $paytoadmin;

								$this->_processSuccessResult ( $payment, $transacionId, $totalAmount );
								$event_data_array = array (
										'transaction_id' => $transacionId,
										'vendor_id' => $vendorId,
										'amount' => $paytovendor,
										'order_id' => $orderID
								);


								$this->saveVpayment($event_data_array, $vendorId, $status );
							}
							else {

								$amount = $baseOrderTotal ['order_total'];
								// echo $baseOrderTotal['order_total'];die(";ghl");
								$paymentToAdmin = $amount + $shippingAmount;
								// $paymentToAdmin =100;
								$createtoken = \Stripe\Token::create ( $Tokenparams );
								try {
									$charge = \Stripe\Charge::create ( array (
											"amount" => $paymentToAdmin * 100, // Amount in cents
											"currency" => $currencyCode,
											"source" => $createtoken->id,
											"description" => "Create Payment",
											"application_fee" => $appFee
									) );
								} catch ( \Stripe\Error\Card $e ) {
									$this->_objectManager->create('Magento\Framework\Exception\PaymentException' )->addException ( __ ( $e->getMessage () ) );
								}
								$transacionId = $charge->id;
								$status = 1;

								$this->_processSuccessResult ( $payment, $transacionId, $paymentToAdmin );
							}

						}
					} catch ( \Exception $e ) {
						throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
					}
				}

				// ** stripe code ends
				return array (
						'status' => 1,
						'fraud' => rand ( 0, 1 ),
						'transaction_id' =>$event_data_array['transaction_id']
				);
			} catch ( Exception $e ) {
				throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
			}
		}



    }

    public function saveVpayment($eventdata, $vendorId, $status) {
    	try {
    		$currencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ( null )->getBaseCurrencyCode ();

    		$model = $this->_objectManager->create ( '\Ced\CsMarketplace\Model\Vpayment' );

    		$transId = $this->_objectManager->create ( '\Ced\CsMarketplace\Model\Vorders' )->load ( $vendorId, 'vendor_id' );
    		$vendor_amount = $eventdata ['amount'];

    		$data ['transaction_id'] = $eventdata ['transaction_id'];
    		$data ['transaction_type'] = 0;
    		$data ['payment_method'] = 1;
    		$data ['vendor_id'] = $eventdata ['vendor_id'];
    		$data ['amount_desc'] = '{"' . $eventdata ['order_id'] . '":"' . $eventdata ['amount'] . '"}';

    		$data ['base_currency'] = $currencyCode;
    		$data ['payment_code'] = 'stripe';
    		$data ['amount'] = $eventdata ['amount'];
    		$data ['base_net_amount'] = $eventdata ['amount'];
    		$data ['net_amount'] = $eventdata ['amount'];
    		$data ['base_amount'] = $eventdata ['amount'];
    		$data ['base_fee'] = '0.00';
    		$data ['tax'] = 0.00;
    		$data ['payment_detail'] = isset ( $data ['payment_detail'] ) ? $data ['payment_detail'] : 'n/a';
    		$data ['status'] = $status;
    		// $model->setData($data);
    		$model->addData ( $data );
    		$openStatus = $model->getOpenStatus ();
    		$model->setStatus ( $openStatus );
    		$model->saveOrders ( $data );
    		$model->save ();
    	} catch ( \Exception $e ) {

    		throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
    	}
    }


    protected function _processSuccessResult($payment, $response, $amount) {
    	$payment->setStatus ( 2 )->setCcTransId ( $response )->setLastTransId ( $response )->setTransactionId ( $response )->setIsTransactionClosed ( 1 )->setCcLast4 ( $payment->getCcNumber () )->setAmount ( $amount )->setShouldCloseParentTransaction ( false );
    	return $payment;
    }

    /**
     * BILLING ADDRESS ARRAY
     */
    public function getBillingAddress($Token, $billingaddress) {
    	$Token ['name'] = $billingaddress->getData ( 'firstname' ) . ' ' . $billingaddress->getData ( 'lastname' );
    	$Token ['address_line1'] = $billingaddress->getData ( 'street' );
    	$Token ['address_city'] = $billingaddress->getData ( 'city' );
    	$Token ['address_state'] = $billingaddress->getData ( 'region' );
    	$Token ['address_zip'] = $billingaddress->getData ( 'postcode' );
    	$Token ['address_country'] = $billingaddress->getData ( 'country_id' );

    	return $Token;
    }
}
