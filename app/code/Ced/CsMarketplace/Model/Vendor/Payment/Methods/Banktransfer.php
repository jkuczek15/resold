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
 
class Banktransfer extends AbstractModel
{
    protected $_code = 'vbanktransfer';
    
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $fields = parent::getFields();
        $fields['bank_name'] = array('type'=>'text');
        $fields['bank_branch_number'] = array('type'=>'text');
        $fields['bank_swift_code'] = array('type'=>'text');
        $fields['bank_account_name'] = array('type'=>'text');
        $fields['bank_account_number'] = array('type'=>'text');
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
            return __('Bank Transfer');break;
        case 'bank_name' : 
            return __('Bank Name');break;
        case 'bank_branch_number' : 
            return __('Bank Branch Number');break;
        case 'bank_swift_code' : 
            return __('Bank Swift Code');break;
        case 'bank_account_name' : 
            return __('Bank Account Name');break;
        case 'bank_account_number' : 
            return __('Bank Account Number');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
}
