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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStripePayment\Model;
//require BP.'/lib/ced/stripe-php-4.3.0/init.php';
use Magento\Framework\Api\AttributeValueFactory;
class SetVendorOrder extends \Ced\CsMarketplace\Model\SetVendorOrder
{



    public function setVendorOrder($order,$orders)
    {
    	//"4242424242424242";
    	try {

    	/* 	$custStripeData = $this->_objectManager->create('Ced\CsStripePayment\Model\Managed');
    	//	$custStripeData->setVendorId(1);
    		$custStripeData->setData('vendor_id',5);
    		$custStripeData->setData('account_id',"kg");
    		$custStripeData->setData('email_id',"devcedcommerce@gmail.com");
    		$custStripeData->setData('secret_key',"fgj");
    		$custStripeData->setData('publishable_key',"fjgfkl");
    		$custStripeData->save();
    		print_r($custStripeData->getData());
    		die("gkh"); */

    	//	die("fkg");



    		$payment = $order->getPayment();
    		$orderID=$order->getIncrementId();
    		$billingaddress=$order->getBillingAddress();
    		$vorder=$this->_objectManager->create('Ced\CsMarketplace\Model\Vorders')->getCollection()->addFieldToFilter('order_id', $order->getIncrementId())->getFirstItem();

    		if($vorder->getId()) {
    			$this->creditMemoOrder($order);
    			return $this;
    		}

    		/* $vendor_data = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings')->getCollection()->addFieldToFilter('vendor_id',)->addFieldToFilter('group','payment')->getData();
    		foreach ($vendor_data as $key=>$value)
    		{
    			if($value['key'] == )
    		} */


    		$products = $order->getItemsCollection()->addFieldToSelect('*');

    		$products = $order->getAllItems();
    		$baseToGlobalRate=$order->getBaseToGlobalRate()?$order->getBaseToGlobalRate():1;
    		$vendorsBaseOrder = array();
    		$vendorQty = array();
    		foreach ($products as $key=>$item) {
    			if($vendor_id = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($item->getProductId())
    			) {
    				$price = 0;

    				$price = $item->getBaseRowTotal()
    				+ $item->getBaseTaxAmount()
    				+ $item->getBaseHiddenTaxAmount()
    				+ $item->getBaseWeeeTaxAppliedRowAmount()
    				- $item->getBaseDiscountAmount();
    				$vendorsBaseOrder[$vendor_id]['order_total'] = isset($vendorsBaseOrder[$vendor_id]['order_total'])?($vendorsBaseOrder[$vendor_id]['order_total'] + $price) : $price;
    				$vendorsBaseOrder[$vendor_id]['item_commission'][$item->getQuoteItemId()] = $price;                                    ;
    				$vendorsBaseOrder[$vendor_id]['order_items'][] = $item;
    				$vendorQty[$vendor_id] = isset($vendorQty[$vendor_id])?$vendorQty[$vendor_id] + $item->getQty() :  $item->getQtyOrdered();

    				$logData = $item->getData();
    				$item->setVendorId($vendor_id);

    				unset($logData['product']);
    			}
    		}

    	} catch (Exception $e) {die(";kl");
    		echo 'New Exception'.$e->getMessage();die;
    	}


    	foreach($vendorsBaseOrder  as $vendorId => $baseOrderTotal){

    		//echo $vendorId; die("h;");
    		$amount=$baseOrderTotal['order_total'];
    		$fee = $baseOrderTotal['item_commission'];
    			//$app_fee=$this->getStoreConfig('payment/csstripe/application_Fee');
    			$app_fee = 1;
    			try{
    					$store = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
    					$mode=$store->getValue('payment/ced_csstripe_method_one/gateway_mode');

    					$skey="api_{$mode}_secret_key";

    					\Stripe\Stripe::setApiKey($store->getValue('payment/ced_csstripe_method_one/'.$skey));

    					if($payment->getMethod()=='ced_csstripe_method_one'){
    					$check_acc= $this->_objectManager->create('Ced\CsStripePayment\Model\Managed')->getCollection()->addFieldToFilter('vendor_id', $vendorId)->addFieldToFilter('email_id', "shikhamishra11@cedcoss.com")->getData();

    					if($check_acc != null){
    					 	$stripe_acc = $check_acc[0]['account_id'];
    					}

    					/**when customer initiates stripe account*/
    					else{

    						if($store->getValue('payment/ced_csstripe_method_one/account_type')=='managed'){
    						try{
    							  $account=\Stripe\Account::create(array(
    							  "managed" 			=> true,
    							  "country"			=> "US",
    							  "email" 			=>"shikhamishra11@cedcoss.com",
    							  	"dob"=> [
    							  		"day"=> null,
    							  		"month"=> null,
    							  		"year"=>null
    							  		],
    							  "external_account" =>[
    							  "object"			=> "bank_account",
    							  "account_number"	=>"000123456789",
    							  "country" 			=>"US",
    							  "currency"			=>"USD",
    							  "routing_number"	=>'110000000'
    							]
    						));
    					}
    					catch(\Exception $e){die("kjks");
    					Mage::throwException($e->getMessage());
    					die;
    					}

    			try{



    				$custStripeData = $this->_objectManager->create('Ced\CsStripePayment\Model\Managed');
    				$custStripeData->setData('vendor_id',$vendorId);
    				$custStripeData->setData('account_id',$account->id);
    				$custStripeData->setData('email_id',"shikhamishra11@cedcoss.com");
    				$custStripeData->setData('secret_key',$account->keys->secret);
    				$custStripeData->setData('publishable_key',$account->keys->publishable);
    				$custStripeData->save();



    			}
    			catch (\Exception $e)
    			{
    				die($e);
    			}

    					$stripe_acc= $account->id;
    		}


    				}
    					$Tokenparams = array(
    					"card" => array(
    					"name" 		=>$billingaddress->getData('firstname').' '.$billingaddress->getData('lastname'),
    					"number" 	=> $payment->getCcNumber(),
    					"cvc" 		=> $payment->getCcCid(),
    					"exp_month" =>$payment->getCcExpMonth(),
    					"exp_year" 	=>$payment->getCcExpYear(),
    					)
    				);

    					$amount = 55;
    					$Tokenparams['card']=$this->getBillingAddress($Tokenparams['card'],$billingaddress);

    					$createtoken1= \Stripe\Token::create($Tokenparams);
    					$createtoken2= \Stripe\Token::create($Tokenparams);
    					 try{
    						$charge1 = \Stripe\Charge::create(
    								array(
    										"amount" => 1000, // amount in cents
    										"currency" => "USD",
    										"source" =>$createtoken1->id,
    										"description" => "Create Payment",
    									//	"application_fee" =>5*100,

    								),
    								array("stripe_account" => $stripe_acc)
    						);


    					}
    					catch(\Stripe\Error\Card $e) {
    					die($e);
    					}


    					try{
    					$charge2 = \Stripe\Charge::create(array(
    							"amount" => 3000, // Amount in cents
    							"currency" => "usd",
    							"source" => $createtoken2->id,
    							"description" => "Create Payment",

    						//	"application_fee" =>2000
    					));
    					} catch(\Stripe\Error\Card $e) {
    						die($e);
    					}


    					print_r($charge2); die("lgkh");
    					$this->_processSuccessResult($payment,$charge->id,$amount);
    					$event_data_array  =  array('transaction_id'=>$charge->id,'vendor_id'=>$vendorId,'amount'=>$amount,'order_id'=>$orderID);
    					}
    				}
    					catch (\Exception $e){die($e);
    						print_r($e->getMessage());
    				}



    		try{

    			$qty = isset($vendorQty[$vendorId])? $vendorQty[$vendorId] : 0;
    			$vorder = $this->_objectManager->create('Ced\CsMarketplace\Model\Vorders');

    			$vorder->setVendorId($vendorId);
    			$vorder->setCurrentOrder($order);
    			$vorder->setOrderId($order->getIncrementId());
    			$vorder->setCurrency($order->getGlobalCurrencyCode());
    			$vorder->setOrderTotal($this->_objectManager->create('Magento\Directory\Helper\Data')->currencyConvert($baseOrderTotal['order_total'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode()));
    			$vorder->setBaseCurrency($order->getBaseCurrencyCode());
    			$vorder->setBaseOrderTotal($baseOrderTotal['order_total']);
    			$vorder->setBaseToGlobalRate($baseToGlobalRate);
    			$vorder->setProductQty($qty);
    			$vorder->setBillingCountryCode($order->getBillingAddress()->getData('country_id'));
    			if($order->getShippingAddress()) {
    				$vorder->setShippingCountryCode($order->getShippingAddress()->getData('country_id'));
    			}
    			$vorder->setItemCommission($baseOrderTotal['item_commission']);
    			$vorder->collectCommission();

    			$this->_eventManager->dispatch(
    					'ced_csmarketplace_vorder_shipping_save_before',
    					[ 'vorder' => $vorder]
    			);

    			$vorder->save();

    			$this->saveVpayment($event_data_array,$vendorId);

    		}
    		catch(Exception $e){
    			echo "Exception: ".$e->getMessage();die;
    		}
    	}

    	try {
    		if($order) {
    			$vorders = $this->_objectManager->create('\Ced\CsMarketplace\Model\Vorders')->getCollection()->addFieldToFilter('order_id', array('eq'=>$order->getIncrementId()));
    			if (count($vorders) > 0) {
    				$this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->sendOrderEmail($order, \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS);
    			}
    		}

    		//$orders = $observer->getOrders();
    		if($orders && is_array($orders)) {
    			foreach($orders as $order){
    				if($order) {
    					$vorders = $this->_objectManager->get('\Ced\CsMarketplace\Model\Vorders')->getCollection()->addFieldToFilter('order_id', array('eq'=>$order->getIncrementId()));
    					if (count($vorders) > 0) {
    						$this->_objectManager->create('Ced\CsMarketplace\Helper\Mail')->sendOrderEmail($order, \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS);
    					}
    				}
    			}
    		}


    	}
    	catch(\Exception $e) {
    		echo "Exception in CsMarketplace::" .$e->getMessage();die('csmarketplace');
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

    public function saveVpayment($eventdata,$vendorId){

    	try{
    		//print_r($eventdata); die("gflb");
    		$model = $this->_objectManager->create('\Ced\CsMarketplace\Model\Vpayment');

    		$transId=$this->_objectManager->create('\Ced\CsMarketplace\Model\Vorders')->load($vendorId,'vendor_id');
    		$vendor_amount= $eventdata['amount']-($transId->getShopCommissionFee());

    		$data['transaction_id']= $eventdata['transaction_id'];
    		$data['transaction_type'] = 0;
    		$data['payment_method'] =1;
    		$data['vendor_id']= $eventdata['vendor_id'];
    		$data['amount_desc']='{"'.$eventdata['order_id'].'":"'.$eventdata['amount'].'"}';

    		$data['base_currency']='USD';
    		$data['payment_code']='ced_csstripe_method_one';
    		$data['amount']=$eventdata['order_id'];
    		$data['base_net_amount'] = $vendor_amount;

    		$data['base_amount']=$vendor_amount;
    		$data['base_fee']='0.00';
    		$data['tax'] = 0.00;
    		$data['payment_detail'] = isset($data['payment_detail'])?$data['payment_detail']:'n/a';
    		$data['status']=2;
    		//$model->setData($data);
    		$model->addData($data);
    		$openStatus = $model->getOpenStatus();
    		$model->setStatus($openStatus);
    		$model->saveOrders($data);
    		$model->save();
    	}
    	catch(\Exception $e){

    		print_r($e->getMessage());
    		die("catch");

    	}
    }



    protected function _processSuccessResult($payment, $response, $amount)
    {
    	$payment->setStatus(2)
    	->setCcTransId($response)
    	->setLastTransId($response)
    	->setTransactionId($response)
    	->setIsTransactionClosed(1)
    	->setCcLast4($payment->getCcNumber())
    	->setAmount($amount)
    	->setShouldCloseParentTransaction(false);
    	return $payment;
    }

}
