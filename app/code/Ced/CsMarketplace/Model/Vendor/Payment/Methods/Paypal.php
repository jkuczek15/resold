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
 
namespace Ced\CsMarketplace\Model\Vendor\Payment\Methods;
 
class Paypal extends AbstractModel
{
    protected $_code = 'vpaypal';
    
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $fields = parent::getFields();
        $fields['paypal_email'] = array('type'=>'text','after_element_html'=>'<a href="http://www.magentocommerce.com/paypal" target="_blank">Start accepting payments via PayPal!</a><script type="text/javascript"> setTimeout(\'if(document.getElementById("'.$this->getCode().$this->getCodeSeparator().'active").value == "1") { document.getElementById("'.$this->getCode().$this->getCodeSeparator().'paypal_email").className = "required-entry validate-email input-text";}\',500);</script>');
        if (isset($fields['active']) && isset($fields['paypal_email'])) {
            $fields['active']['onchange'] = "if(this.value == '1') { document.getElementById('".$this->getCode().$this->getCodeSeparator()."paypal_email').className = 'required-entry validate-email input-text';} else { document.getElementById('".$this->getCode().$this->getCodeSeparator()."paypal_email').className = 'input-text'; } ";
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
        case 'label' : 
            return __('PayPal');break;
        case 'paypal_email' : 
            return __('Email Associated with PayPal Merchant Account');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
}
