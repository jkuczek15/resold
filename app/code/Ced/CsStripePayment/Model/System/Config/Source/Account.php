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
namespace Ced\CsStripePayment\Model\System\Config\Source;

use Ced\CsStripePayment\Model\MethodOne;
class Account implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
    {
    	
    	 
        return [
          [
                'value' => 'managed',
                'label' => __('managed')
           ],
            [
            'value' => 'standalone',
            		'label' => __('standalone')
            		],
        
            ];
    }
}