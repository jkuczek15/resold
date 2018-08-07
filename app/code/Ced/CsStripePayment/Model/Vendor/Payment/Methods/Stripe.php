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
namespace Ced\CsStripePayment\Model\Vendor\Payment\Methods;
 
class Stripe extends \Ced\CsMarketplace\Model\Vendor\Payment\Methods\AbstractModel
{
    protected $_code = 'vstripe';
    
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
		$fields = parent::getFields();
		
		$ob =  \Magento\Framework\App\ObjectManager::getInstance();
		$store = $ob->get('Magento\Framework\App\Config\ScopeConfigInterface');
		$clientIdMode = $store->getValue('payment/ced_csstripe_method_one/client_id_mode');
		if($clientIdMode=='Development')
		{
			$clientId= $store->getValue('payment/ced_csstripe_method_one/client_did');
		}
		else
		{
			$clientId = $store->getValue('payment/ced_csstripe_method_one/client_pid');
		}
			
		
		$urlbuilder = $ob->create('Magento\Framework\UrlInterface');
		$redirect_uri= $urlbuilder->getUrl('csstripe/index/index');
		
		$url='https://connect.stripe.com/oauth/authorize?response_type=code&client_id='.$clientId.'&scope=read_write';
		
		if($store->getValue('payment/ced_csstripe_method_one/account_type')=='standalone'){
			$fields['']=array('type'=>'text',
					'class'=>'hide',
					'after_element_html'=>'<a class="btn btn-primary uptransform" href='.$url.'><font color="white">Connect With Stripe</font></a>'
			);
		}
		
		else{
			$fields['stripe_email'] = array('type'=>'text','after_element_html'=>'<a href="https://stripe.com/docs" target="_blank">Start accepting payments via Stripe!</a><script type="text/javascript"> setTimeout(\'if(document.getElementById("'.$this->getCode().$this->getCodeSeparator().'active").value == "1") { document.getElementById("'.$this->getCode().$this->getCodeSeparator().'stripe_email").className = "required-entry validate-email input-text";}\',500);</script>');
			if (isset($fields['active']) && isset($fields['stripe_email'])) {
				$fields['active']['onchange'] = "if(this.value == '1') { document.getElementById('".$this->getCode().$this->getCodeSeparator()."stripe_email').className = 'required-entry validate-email input-text';} else { document.getElementById('".$this->getCode().$this->getCodeSeparator()."stripe_email').className = 'input-text'; } ";
			}
			$fields['account_number'] = array('type'=>'text');
			$fields['routing_number'] = array('type'=>'text');
		}
		
	
		return $fields;
	}
    
    /**
     * Retreive labels
     *
     * @param  string $key
     * @return string
     */
    public function getLabel($key) 
    {
		switch($key) {
			case 'label' : return __('Stripe Payment');break;
			case 'stripe_email' : return __('Email Associated with Stripe Merchant Account');break;
			case 'account_number'	:return __('Bank Account Number');break;
			case 'routing_number'	:return __('Routing Number');break;
			default : return parent::getLabel($key); break;
		}
	}
}
