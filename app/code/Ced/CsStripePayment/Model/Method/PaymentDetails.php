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
namespace Ced\CsStripePayment\Model\Method;

use Magento\Setup\Module\Dependency\Parser\Composer\Json;
/**
 * @codeCoverageIgnoreStart
 */
class PaymentDetails extends \Magento\Checkout\Model\PaymentDetails
{
    /**
     * @{inheritdoc}
     */

    protected $_scopeConfig;
    protected $_quote;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $quote
    ) {
        $this->_quote = $quote;
        $this->_scopeConfig = $scopeConfig;

    }

    public function getPaymentMethods()
    {
        $vendorId = [];
        $Quote = $this->_quote->getQuote()->getAllVisibleItems();
        foreach($Quote as $item)
        {
        	$productID = $item->getProductId();
        	$productSku = $item->getSku();
        	$productName = $item->getName();
        	$productQty = $item->getQty();
        	$price=$item->getPrice();
        	$vendorId[]=$item->getVendorId();
        	$shipping=$item->getShippingAmount();
        }

          $flag = false;
	      foreach ($vendorId as $val)
	      {
	      	if($val != null)
	      	{
	      		$flag = true;
	      	}

	      }


        $json_obj = $this->getData(self::PAYMENT_METHODS);
        $unset_queue = array();
        $paymethod = ['ced_csstripe_method_one'];

        if($flag == false) {
            foreach ( $json_obj as $i => $item )
            {

                    if (in_array($item->getCode(), $paymethod)) {
                        $unset_queue[] = $i;
                    }

            }

            foreach ( $unset_queue as $index )
            {
                unset($json_obj[$index]);
            }

            $json_obj = array_values($json_obj);
            return $json_obj;
        }
        else
        {
            return $this->getData(self::PAYMENT_METHODS);
        }
    }

}
