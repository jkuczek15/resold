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

class Cheque extends AbstractModel
{
    protected $_code = 'vcheque';
    
    /**
     * Retreive input fields
     *
     * @return array
     */
    public function getFields() 
    {
        $fields = parent::getFields();
        $fields['cheque_payee_name'] = array('type'=>'text');
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
            return __('Check/Money Order');break;
        case 'cheque_payee_name' : 
            return __('Cheque Payee Name');break;
        default : 
            return parent::getLabel($key); break;
        }
    }
}
