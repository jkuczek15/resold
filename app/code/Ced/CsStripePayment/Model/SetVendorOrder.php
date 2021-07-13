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
 * @category    Ced
 * @package     Ced_CsStripePayment
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStripePayment\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Mirasvit\SearchElastic\Model\Engine;

class SetVendorOrder extends \Ced\CsMarketplace\Model\SetVendorOrder {

		protected $logger;
		protected $request;

    /**
     * @var Engine
     */
    protected $engine;

		public function __construct(
				\Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
		    \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Webapi\Rest\Request $request,
        Engine $engine
		)
		{
			$this->request = $request;
		    $this->logger = $logger;
				$this->engine = $engine;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
				parent::__construct($objectInterface, $eventManager);
		}

    /**
     * @return \Elasticsearch\Client
     */
    public function getClient()
    {
        return $this->client;
    }

	public function setVendorOrder($order) {

		try {
			$currencyCode = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ( null )->getBaseCurrencyCode ();
			$country = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( 'general/country/default' );

			$payment = $order->getPayment ();

			$orderID = $order->getIncrementId ();

			$billingaddress = $order->getBillingAddress ();
			$vorder = $this->_objectManager->create ( 'Ced\CsMarketplace\Model\Vorders' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getIncrementId () )->getFirstItem ();

			if ($vorder->getId ()) {
				$this->creditMemoOrder ( $order );
				return $this;
			}

			$products = $order->getItemsCollection ()->addFieldToSelect ( '*' );

			$products = $order->getAllItems ();
			$baseToGlobalRate = $order->getBaseToGlobalRate () ? $order->getBaseToGlobalRate () : 1;
			$vendorsBaseOrder = array ();
			$vendorQty = array ();
			$vendorId = '';
			foreach ( $products as $key => $item ) {
				if ($vendor_id = $this->_objectManager->create ( 'Ced\CsMarketplace\Model\Vproducts' )->getVendorIdByProduct ( $item->getProductId () )) {
					$price = 0;

					$price = $item->getBaseRowTotal () + $item->getBaseTaxAmount () + $item->getBaseHiddenTaxAmount () + $item->getBaseWeeeTaxAppliedRowAmount () - $item->getBaseDiscountAmount ();
					$vendorsBaseOrder [$vendor_id] ['order_total'] = isset ( $vendorsBaseOrder [$vendor_id] ['order_total'] ) ? ($vendorsBaseOrder [$vendor_id] ['order_total'] + $price) : $price;
					$vendorsBaseOrder [$vendor_id] ['item_commission'] [$item->getQuoteItemId ()] = $price;
					$vendorsBaseOrder [$vendor_id] ['order_items'] [] = $item;
					$vendorQty [$vendor_id] = isset ( $vendorQty [$vendor_id] ) ? $vendorQty [$vendor_id] + $item->getQty () : $item->getQtyOrdered ();

					$logData = $item->getData ();
					$item->setVendorId ( $vendor_id );
					$vendorId = $vendor_id;

					// remove the product from all categories to mark the order as complete
	        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
					$product->setCategoryIds([]);
					$product->setVisibility(2);
					$product->save();

					// delete product from elastic search index
					$this->engine->deleteDocuments('catalogsearch_fulltext_scope1', [ $item->getProductId() ]);

					unset ( $logData ['product'] );
				}
			}
		} catch ( \Exception $e ) {
			throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage() ) );
		}

		// get the seller info
    $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
		$seller = $vendor->getCustomer();

		$count = 0;
		foreach ( $vendorsBaseOrder as $vendorId => $baseOrderTotal ) {
			$count ++;
			if ($count == 1) {
				$shippingAmount = $order->getShippingAmount ();
			} else {
				$shippingAmount = 0;
			}

			try {

				$qty = isset ( $vendorQty [$vendorId] ) ? $vendorQty [$vendorId] : 0;
				$vorder = $this->_objectManager->create ( 'Ced\CsMarketplace\Model\Vorders' );

				$vorder->setVendorId ( $vendorId );
				$vorder->setCurrentOrder ( $order );
				$vorder->setOrderId ( $order->getIncrementId () );
				$vorder->setCurrency ( $order->getGlobalCurrencyCode () );
				$vorder->setOrderTotal ( $this->_objectManager->create ( 'Magento\Directory\Helper\Data' )->currencyConvert ( $baseOrderTotal ['order_total'], $order->getBaseCurrencyCode (), $order->getGlobalCurrencyCode () ) );
				$vorder->setBaseCurrency ( $order->getBaseCurrencyCode () );
				$vorder->setBaseOrderTotal ( $baseOrderTotal ['order_total'] );
				$vorder->setBaseToGlobalRate ( $baseToGlobalRate );
				$vorder->setProductQty ( $qty );
				$billingadd = $order->getBillingAddress ()->getData ();
				if (isset ( $billingaddress ['middlename'] )) {
					$billing_name = $billingadd ['firstname'] . " " . $billingadd ['middlename'] . " " . $billingadd ['lastname'];
				} else {
					$billing_name = $billingadd ['firstname'] . " " . $billingadd ['lastname'];
				}
				$vorder->setBillingName ( $billing_name );

				$vorder->setBillingCountryCode ( $order->getBillingAddress ()->getData ( 'country_id' ) );

				if ($order->getShippingAddress ()) {
					$vorder->setShippingCountryCode ( $order->getShippingAddress ()->getData ( 'country_id' ) );
				}
				$vorder->setItemCommission ( $baseOrderTotal ['item_commission'] );
				$vorder->collectCommission ();

				$this->_eventManager->dispatch ( 'ced_csmarketplace_vorder_shipping_save_before', [
						'vorder' => $vorder
				]);

				// ** stripe code starts
				$store = $this->_objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' );
				if (($payment->getMethod () == 'stripe') && ($store->getValue ( 'payment/ced_csstripe_method_one/payment_action' ) != "authorize")) {

					try {

						$mode = $store->getValue ( 'payment/ced_csstripe_method_one/gateway_mode' );
						$appFee = $store->getValue ( 'payment/ced_csstripe_method_one/app_fee' );
						$skey = "api_{$mode}_secret_key";

						\Stripe\Stripe::setApiKey ( $store->getValue ( 'payment/ced_csstripe_method_one/' . $skey ) );

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

							$getData = $this->_objectManager->create( 'Ced\CsStripePayment\Model\Standalone' )->getCollection ()->getData();

							/**
							 * get token, if Mode is set to standalone
							 */
							$has_stripe_account = false;
							$stripe_account = null;
							foreach ( $getData as $get ) {
								if ($vendorId == $get ['vendor_id']) {
									$has_stripe_account = true;
									$stripe_account = $get ['stripe_user_id'];
									$token = $get ['access_token'];
								}// end if we found the seller's Stripe account
							}// end foreach finding stripe account

							$stripe_acc = $stripe_account;

							$Tokenparams = array (
									"card" => array (
											"name" => $billingaddress->getData ( 'firstname' ) . ' ' . $billingaddress->getData ( 'lastname' ),
											"number" => $payment->getCcNumber (),
											"cvc" => $payment->getCcCid (),
											"exp_month" => $payment->getCcExpMonth (),
											"exp_year" => $payment->getCcExpYear ()
									)
							);

							$Tokenparams ['card'] = $this->getBillingAddress ( $Tokenparams ['card'], $billingaddress );

							$amount = $baseOrderTotal ['order_total'];
							$total = ($amount + $shippingAmount) * 100;

							$token = $payment->getAdditionalInformation('token');
							if($token == null) {
								$createtoken = \Stripe\Token::create ( $Tokenparams );
							}

							$items = $order->getAllItems();
							$product = $items[0]->getProduct();

							$post = $this->request->getBodyParams();
							if(isset($post['delivery_fee']) && $post['delivery_fee'] != null) {
								$resold_fee = $post['delivery_fee'];
							}else {
								$resold_fee = ceil($total * ($appFee / 100));
							}

							if($resold_fee > 5000){
								$resold_fee = 5000;
							}

							if($has_stripe_account){
								// create a direct charge and pay the seller immediately
								try {
									$charge = \Stripe\Charge::create ( array (
											"amount" => $total + $resold_fee, // Amount in cents
											"currency" => $currencyCode,
											"source" => $token != null ? $token : $createtoken->id,
											"description" => "Resold Payment - ".$product->getName(),
											"application_fee" => $resold_fee,
											"receipt_email" => $order->getCustomerEmail()
										), array (
											"stripe_account" => $stripe_acc
									));

									$product->setCustomAttribute('charge_id', $charge->id);
									$product->save();
								} catch ( \Stripe\Error\Card $e ) {
									throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
								}// end try-catch creating a charge
							}else{
								// the seller hasn't connected to Stripe
								// create a separate charge and transfer
								try {
									$charge = \Stripe\Charge::create ( array (
											"amount" => $total + $resold_fee, // Amount in cents
											"currency" => $currencyCode,
											"source" => $token != null ? $token : $createtoken->id,
											"description" => "Resold Payment - ".$product->getName(),
											"receipt_email" => $order->getCustomerEmail(),
											"transfer_group" => $product->getId()
									));
									$product->setCustomAttribute('charge_id', $charge->id);
									$product->save();

									// send an email to the seller telling them to connect to Stripe to receive funds
					        try {
					          // send an email to the user letting them know they need to connect to stripe
					          $this->inlineTranslation->suspend();
					          // send the customer an email telling them to connect with stripe
					          $sender = [
					            'name' => 'Resold',
					            'email' => 'support@resold.us'
					          ];

					          $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
					          $transport = $this->_transportBuilder
					            ->setTemplateIdentifier('get_paid_template') // this code we have mentioned in the email_templates.xml
					            ->setTemplateOptions([
					              'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
					              'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
					          ])
					          ->setTemplateVars(['host' => $_SERVER['HTTP_HOST'], 'name' => $seller->getName(), 'total' => '$'.trim(money_format('%(#10n', (($total - $resold_fee - ceil(0.029 * $total + 30)) / 100))) ])
					          ->setFrom($sender)
					          ->addTo($seller->getEmail())
					          ->getTransport();

					          $transport->sendMessage();
					          $this->inlineTranslation->resume();
					        } catch(\Exception $e){
										$this->logger->critical($e->getMessage());
					          $this->inlineTranslation->resume();
					        }// end try catch
								} catch ( Exception $e ) {
									$this->logger->critical($e->getMessage());
									throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
								}// end try-catch creating a charge
							}// end if the seller is connected with Stripe

							$sku = $product->getSku();
							$code = substr($sku, 0, 3);
							$product_id = substr($sku, 4);
							if($code == 'amz')
							{
								// place an order with Zinc on Amazon
					      // Zinc API client ID and base url
					      $base_url = 'https://api.zinc.io/v1/';
					      $encoded_client_id = '<your client ID>';

					      // CURL request to retreive product data
					      $curl = curl_init();

					      // setup additonal CURL options
					      curl_setopt_array($curl, array(
					          CURLOPT_RETURNTRANSFER => 1,
					          CURLOPT_URL => $base_url.'orders'
					      ));

					      // set authorization header
					      $headers = array(
					          'Content-Type: application/json',
					          'Authorization: Basic '.$encoded_client_id
					      );
					      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

								// json encode the ZINC order data
								$shipping_address = $order->getShippingAddress();
								$order_data = [
									'retailer' => 'amazon',
									'products' => [[
										'product_id' => $product_id,
										'quantity' => 1
									]],
									'shipping_address' => [
										'first_name' => $shipping_address->getFirstname(),
										'last_name' => $shipping_address->getLastname(),
										'address_line1' => $shipping_address->getStreet(),
										'address_line2' => '',
										'zip_code' => $shipping_address->getData('postcode'),
										'city' => $shipping_address->getCity(),
										'state' => $shipping_address->getData('region'),
										'country' => $shipping_address->getData('country_id'),
										'phone_number' => '<your phone number>'
									],
									'shipping_method' => 'cheapest',
									'payment_method' => [
										'name_on_card' => '<your name>',
										'number' => '<your card number>',
										'security_code' => '<your security code>',
										'expiration_month' => '<your expiration month>',
										'expiration_year' => '<your expiration year>'
									],
									'billing_address' => [
										'first_name' => '<your first name>',
										'last_name' => '<your last name>',
										'address_line1' => '<your address>',
										'address_line2' => '',
										'zip_code' => '<your zip code>',
										'city' => '<your city>',
										'state' => '<your state>',
										'country' => '<your country>',
										'phone_number' => '<your phone number>'
									],
									'retailer_credentials' => [
										'email' => '<your email>',
										'password' => '<your password>'
									]
								];
								$payload = json_encode($order_data);
								curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

					      // make CURL request
					      $result = curl_exec($curl);

								// DEBUG: Amazon ZINC requests
								// $this->logger->critical("==========================");
								// $this->logger->critical($product_id);
								// $this->logger->critical($payload);
								// $this->logger->critical(curl_error($curl));
	              // $this->logger->critical($result);
								// $this->logger->critical("==========================");

					      // close CURL session
					      curl_close ($curl);
							}// end if amazon listed product

							$transacionId = $charge->id;
							$status = 1;
							$this->_processSuccessResult ( $payment, $transacionId, $total );
						}// end if payment method stripe
					} catch ( \Exception $e ) {
						throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
					}
				}

				// ** stripe code ends
				$vorder->save ();
			} catch ( Exception $e ) {
				throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
			}
		}

		try {
			if ($order) {
				$vorders = $this->_objectManager->create ( '\Ced\CsMarketplace\Model\Vorders' )->getCollection ()->addFieldToFilter ( 'order_id', array (
						'eq' => $order->getIncrementId ()
				) );
				if (count ( $vorders ) > 0) {
					$this->_objectManager->get ( 'Ced\CsMarketplace\Helper\Mail' )->sendOrderEmail ( $order, \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS, $vendorId, $order );
				}
			}
		} catch ( \Exception $e ) {
			throw new \Magento\Framework\Exception\LocalizedException ( __ ( $e->getMessage () ) );
		}
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
			$data ['payment_code'] = 'ced_csstripe_method_one';
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
}
