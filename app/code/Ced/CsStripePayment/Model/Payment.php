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
use Magento\Framework\Api\AttributeValueFactory;
class Payment extends \Ced\CsMarketplace\Model\Vendor
{
    
	public function getPaymentMethods($vendorId = 0) {
		
		if(!$vendorId && $this->getId())
		{
			$vendorId = $this->getId();
		} 
		$availableMethods = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Paymentmethods')->toOptionArray();
		$methods = array();
		if (count($availableMethods)>0) {
			foreach($availableMethods as $method) {
				if (isset($method['value'])) {
						
					if($method['value']!='stripe')
						$object = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Payment\Methods\\'.ucfirst($method['value']));
	
					else
						$object = $this->_objectManager->get('Ced\CsStripePayment\Model\Vendor\Payment\Methods\\'.ucfirst($method['value']));
	
					if(is_object($object)) {
	
						$methods[$method['value']] = $object;
					}
				}
			}
		}
	
		return $methods;
	}

}
