<?php
/**
 * @category  Apptrian
 * @package   Apptrian_FacebookPixel
 * @author    Apptrian
 * @copyright Copyright (c) 2018 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Apptrian\FacebookPixel\Block;

class Code extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @var \Apptrian\FacebookPixel\Helper\Data
     */
    public $helper;
    
    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;
    
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    public $catalogHelper;
    
    /**
     * Tax config model
     *
     * @var \Magento\Tax\Model\Config
     */
    public $taxConfig;
    
    /**
     * Tax display flag
     *
     * @var null|int
     */
    public $taxDisplayFlag = null;
    
    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    public $taxCatalogFlag = null;
    
    /**
     * Store object
     *
     * @var null|\Magento\Store\Model\Store
     */
    public $store = null;
    
    /**
     * Store ID
     *
     * @var null|int
     */
    public $storeId = null;
    
    /**
     * Base currency code
     *
     * @var null|string
     */
    public $baseCurrencyCode = null;
    
    /**
     * Current currency code
     *
     * @var null|string
     */
    public $currentCurrencyCode = null;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Apptrian\FacebookPixel\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Apptrian\FacebookPixel\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->storeManager  = $context->getStoreManager();
        $this->helper        = $helper;
        $this->coreRegistry  = $coreRegistry;
        $this->catalogHelper = $catalogHelper;
        $this->taxConfig     = $taxConfig;
        parent::__construct($context, $data);
    }
    
    /**
     * Used in .phtml file and returns array of data.
     *
     * @return array
     */
    public function getFacebookPixelData()
    {
        $data = [];
    
        $data['id'] = $this->helper->getConfig(
            'apptrian_facebookpixel/general/pixel_id',
            $this->getStoreId()
        );
    
        $data['full_action_name'] = $this->getRequest()->getFullActionName();
    
        return $data;
    }
    
    /**
     * Returns product data needed for dynamic ads tracking.
     *
     * @return array
     */
    public function getProductData()
    {
        $p = $this->coreRegistry->registry('current_product');
    
        $data = [];
    
        $data['content_name']     = $this->helper
            ->escapeSingleQuotes($p->getName());
        $data['content_ids']      = $this->helper
            ->escapeSingleQuotes($p->getSku());
        $data['content_type']     = 'product';
        $data['value']            = $this->formatPrice(
            $this->getProductPrice($p)
        );
        $data['currency']         = $this->getCurrentCurrencyCode();
    
        return $data;
    }
    
    /**
     * Returns store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();
        }
        
        return $this->store;
    }
    
    /**
     * Returns Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->getStore()->getId();
        }
        
        return $this->storeId;
    }
    
    /**
     * Returns base currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        if ($this->baseCurrencyCode === null) {
            $this->baseCurrencyCode = strtoupper(
                $this->getStore()->getBaseCurrencyCode()
            );
        }
        
        return $this->baseCurrencyCode;
    }
    
    /**
     * Returns current currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if ($this->currentCurrencyCode === null) {
            $this->currentCurrencyCode = strtoupper(
                $this->getStore()->getCurrentCurrencyCode()
            );
        }
        
        return $this->currentCurrencyCode;
    }
    
    /**
     * Returns flag based on "Stores > Cofiguration > Sales > Tax
     * > Price Display Settings > Display Product Prices In Catalog"
     * Returns 0 or 1 instead of 1, 2, 3.
     *
     * @return int
     */
    public function getDisplayTaxFlag()
    {
        if ($this->taxDisplayFlag === null) {
            // Tax Display
            // 1 - excluding tax
            // 2 - including tax
            // 3 - including and excluding tax
            $flag = $this->taxConfig->getPriceDisplayType($this->getStoreId());
            
            // 0 means price excluding tax, 1 means price including tax
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
        }
        
        return $this->taxDisplayFlag;
    }
    
    /**
     * Returns Stores > Cofiguration > Sales > Tax > Calculation Settings
     * > Catalog Prices configuration value
     *
     * @return int
     */
    public function getCatalogTaxFlag()
    {
        // Are catalog product prices with tax included or excluded?
        if ($this->taxCatalogFlag === null) {
            $this->taxCatalogFlag = (int) $this->helper->getConfig(
                'tax/calculation/price_includes_tax',
                $this->getStoreId()
            );
        }
        
        // 0 means excluded, 1 means included
        return $this->taxCatalogFlag;
    }
    
    /**
     * Returns product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $price = 0.0;
        
        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }
        
        return $price;
    }
    
    /**
     * Returns bundle product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();
        
        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }
    
    /**
     * Returns configurable product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $simpleCollection = $product->getTypeInstance()
                ->getUsedProducts($product);
            
            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }
        
        return $this->getFinalPrice($product);
    }
    
    /**
     * Returns grouped product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');
        
        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }
        
        return $minPrice;
    }
    
    /**
     * Returns final price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $price
     * @return string
     */
    public function getFinalPrice($product, $price = null)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }
        
        if ($price === null) {
            $price = $product->getData('special_price');
        }
        
        $productType = $product->getTypeId();
        
        // 1. Convert to current currency if needed
        
        // Convert price if base and current currency are not the same
        // Except for configurable products they already have currency converted
        if (($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode())
            && $productType != 'configurable'
        ) {
            // Convert to from base currency to current currency
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        
        // 2. Apply tax if needed
        
        // Simple, Virtual, Downloadable products price is without tax
        // Grouped products have associated products without tax
        // Bundle products price already have tax included/excluded
        // Configurable products price already have tax included/excluded
        if ($productType != 'configurable' && $productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }
        }
        
        // Case when catalog prices are with tax but display tax is set to
        // to exclude tax. Applies for all products except for bundle
        if ($productType != 'bundle') {
            // If display tax flag is off and catalog tax flag is on
            if (!$this->getDisplayTaxFlag() && $this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }
        
        return $price;
    }
    
    /**
     * Returns formated price.
     *
     * @param string $price
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $currencyCode = '')
    {
        $formatedPrice = number_format($price, 2, '.', '');
        
        if ($currencyCode) {
            return $formatedPrice . ' ' . $currencyCode;
        } else {
            return $formatedPrice;
        }
    }
}
