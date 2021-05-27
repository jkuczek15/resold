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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Plugin;

use Magento\Store\Model\ScopeInterface;

class SetShippingMethodForProduct
{
    public $_scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
	  {
	    $this->_scopeConfig = $scopeConfig;
	  }

  /*
  * $subject -
  * $result - Magento\Shipping\Model\Shipping\Interceptor
  */
  public function afterCollectRates(\Magento\Shipping\Model\Shipping $subject, $result)
  {
      $cart_items = $result->getAllItems();

      // get the actual product with custom attributes
      $product_id = $cart_items[0]->getProductId();
      $model = \Magento\Framework\App\ObjectManager::getInstance();
      $product = $model->create('Magento\Catalog\Model\Product')->load($product_id);

      // retreive local global attributes for the cart item
      $local_global = explode(',', $product->getData('local_global'));

      // modify the tmp result based on local/global
      $tmpResult = clone $subject->getResult();
      $tmpRates = [];
      $rates = $tmpResult->getAllRates();

      $local_id = '231';
      $global_id = '232';
      if(in_array($local_id, $local_global)){
          // local product
          $tmpRates = array_merge($tmpRates, $tmpResult->getRatesByCarrier('flatrate'));
      }// end if local product

      if(in_array($global_id, $local_global)){
        // global product
        $tmpRates = array_merge($tmpRates, $tmpResult->getRatesByCarrier('freeshipping'));
      }// end if global product

      $subject->getResult()->reset();
      $subject->getResult()->setRates($tmpRates);
      return $subject;
  }
}
