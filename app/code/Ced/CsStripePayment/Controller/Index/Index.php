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
namespace Ced\CsStripePayment\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
    
    	
        $ob = $this->_objectManager;
        $store = $ob->get('Magento\Framework\App\Config\ScopeConfigInterface');
  		if($store->getValue('payment/ced_csstripe_method_one/account_type')=='standalone'){
	    	if (isset($_GET['code'])) { 
	    	
	    		$TOKEN_URI='https://connect.stripe.com/oauth/token';
	    		$code = $_GET['code'];
	    		$clientIdMode=$store->getValue('payment/ced_csstripe_method_one/client_id_mode');
	    		if($clientIdMode=='Development')
	    			$clientId=$store->getValue('payment/ced_csstripe_method_one/client_did');
	    		else
	    			$clientId=$store->getValue('payment/ced_csstripe_method_one/client_pid');
	    	
	    		$mode=$store->getValue('payment/ced_csstripe_method_one/gateway_mode');
	    	
	    		/** get API mode test/live */
	    		$skey="api_{$mode}_secret_key";
	    	
	    		try{
	    		$key=$store->getValue('payment/ced_csstripe_method_one/'.$skey);
	    		
	       		$token_request_body = array(
	    				'grant_type' => 'authorization_code',
	    				'client_id' => $clientId,
	    				'code' => $code,
	    				'client_secret' =>$key
	    		);
	    		/*
	    		 Array ( [access_token] => sk_test_6eJ53weZmD6wdefcJDnfP4Dg [livemode] => [refresh_token] => rt_8VOzR1nDhrSpfBM8LPYrFRjiM2Wmeseyg49ZwP04xsjtmdjb [token_type] => bearer [stripe_publishable_key] => pk_test_yCEEPiKjXyOtIuHRnbE8lz6F [stripe_user_id] => acct_188F18Iw2Ylw9dxv [scope] => read_write ) sk_test_6eJ53weZmD6wdefcJDnfP4Dgdfsf
	    		 */
	    		$req = curl_init('https://connect.stripe.com/oauth/token');
				curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($req, CURLOPT_POST, true );
				curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));    		 
	    		
	    		$respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
	    		$resp = json_decode(curl_exec($req), true);
	    		curl_close($req);
	    		
	    	
	    		$vendorId=$this->_getSession()->getVendorId();
	    	
	    		$model=$this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
	    		
	    		$model1=$model->load($vendorId,'vendor_id')->getData();
	    		
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
		    				echo "Data updated successfully.";
		    				$this->_redirect('csmarketplace/vsettings/index');
		    			
		    			} catch (\Exception $e){
		    				echo $e->getMessage();
		    			}
		    			
		    		}
		    		else{
		    			
			    		$model->setData('access_token',$resp['access_token'])
			    				->setData('refresh_token',$resp['refresh_token'])
			    				->setData('token_type',$resp['token_type'])
			    				->setData('stripe_publishable_key',$resp['stripe_publishable_key'])
			    				->setData('stripe_user_id',$resp['stripe_user_id'])
			    				->setData('scope',$resp['scope'])
			    				->setData('vendor_id',$vendorId)
			    				->save();
			    	//	$this->_getSession()->addSuccess($this->__('You have successfully connected to STRIPE Account'));
			    		$this->_redirect('csmarketplace/vsettings/index');
			    		return;
		    		}
	    		}
	    		catch(\Exception $e){
	    			
	    			//$this->_objectManager->create('Magento\Customer\Model\Session')->addError($e->getMessage());
	    			$this->_redirect('csmarketplace/vsettings/index');
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
   		
    }
    
    
    protected function _getSession()
    {
    	return $this->_objectManager->create('Magento\Customer\Model\Session');
    }
}

