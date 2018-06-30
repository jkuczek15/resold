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

namespace Ced\CsMarketplace\Block\Cart;

class Shipping extends \Magento\Framework\View\Element\Template
{
    /**
     * Available Carriers Instances
     * @var null|array
     */
    protected $_carriers = null;

    /**
     * Estimate Rates
     * @var array
     */
    protected $_rates = array();

    protected $_checkout = null;
    protected $_quote    = null;
    
    /**
     * Address Model
     *
     * @var array
     */
    protected $_address = array();
    
    protected $_objectManager;
    
    public function __construct(
    	 \Magento\Catalog\Block\Product\Context $context,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
    		array $data = []
    ){
    	  $this->_objectManager = $objectManager;
    	  parent::__construct($context,$data);
    }
    
    /**
     * Get Estimate Rates
     *
     * @return array
     */
    public function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }

    /**
     * Get Address Model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Get Carrier Name
     *
     * @param string $carrierCode
     * @return mixed
     */
    public function getCarrierName($carrierCode)
    {
        if ($name = $this->getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    
    public  function getStoreConfig($path)
    {
    	$scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
    	$value =  $scopeConfig->getValue(
    			$path,
    			\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	return $value;
    }
    /**
     * Get Shipping Method
     *
     * @return string
     */
    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * Get Estimate Country Id
     *
     * @return string
     */
//     public function getEstimateCountryId()
//     {
//         return $this->getAddress()->getCountryId();
//     }

    /**
     * Get Estimate Postcode
     *
     * @return string
     */
    public function getEstimatePostcode()
    {
        return $this->getAddress()->getPostcode();
    }

    /**
     * Get Estimate City
     *
     * @return string
     */
    public function getEstimateCity()
    {
        return $this->getAddress()->getCity();
    }

    /**
     * Get Estimate Region Id
     *
     * @return mixed
     */
    public function getEstimateRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    /**
     * Get Estimate Region
     *
     * @return string
     */
    public function getEstimateRegion()
    {
        return $this->getAddress()->getRegion();
    }

    /**
     * Show City in Shipping Estimation
     *
     * @return bool
     */
    public function getCityActive()
    {
        return (bool)$this->getStoreConfig('carriers/dhl/active')
            || (bool)$this->getStoreConfig('carriers/dhlint/active');
    }

    /**
     * Show State in Shipping Estimation
     *
     * @return bool
     */
    public function getStateActive()
    {
        return (bool)$this->getStoreConfig('carriers/dhl/active')
            || (bool)$this->getStoreConfig('carriers/tablerate/active')
            || (bool)$this->getStoreConfig('carriers/dhlint/active');
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->convertPrice($price, true);
    }

    /**
     * Get Shipping Price
     *
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function getShippingPrice($price, $flag)
    {
        return $this->formatPrice($this->helper('tax')->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            $this->getQuote()->getCustomerTaxClassId()
        ));
    }

    /**
     * Obtain available carriers instances
     *
     * @return array
     */
    public function getCarriers()
    {
        if (null === $this->_carriers) {
            $this->_carriers = array();
            $this->getEstimateRates();
            foreach ($this->_rates as $rateGroup) {
                if (!empty($rateGroup)) {
                    foreach ($rateGroup as $rate) {
                        $this->_carriers[] = $rate->getCarrierInstance();
                    }
                }
            }
        }
        return $this->_carriers;
    }

    /**
     * Check if one of carriers require state/province
     *
     * @return bool
     */
    public function isStateProvinceRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isStateProvinceRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require city
     *
     * @return bool
     */
    public function isCityRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isCityRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require zip code
     *
     * @return bool
     */
    public function isZipCodeRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isZipCodeRequired($this->getEstimateCountryId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return 'Estimate Shipping and Tax' form action url
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/estimatePost', array('_secure' => $this->_isSecure()));
    }

    /**
     * Return 'Update Estimate Shipping and Tax' form action url
     *
     * @return string
     */
    public function getUpdateFormActionUrl()
    {
        return $this->getUrl('checkout/cart/estimateUpdatePost', array('_secure' => $this->_isSecure()));
    }
    
    /**
     * Get base url for block.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getBaseUrl()
    {
    	return $this->_storeManager->getStore()->getBaseUrl();
    }
    
    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
    	if (null === $this->_checkout) {
    		$this->_checkout = $this->_objectManager->get('Magento\Checkout\Model\Session');
    	}
    	return $this->_checkout;
    }
    
    /**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
    	if (null === $this->_quote) {
    		$this->_quote = $this->getCheckout()->getQuote();
    	}
    	return $this->_quote;
    }
    
}
