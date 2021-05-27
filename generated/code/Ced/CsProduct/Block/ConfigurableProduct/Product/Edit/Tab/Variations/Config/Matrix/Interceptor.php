<?php
namespace Ced\CsProduct\Block\ConfigurableProduct\Product\Edit\Tab\Variations\Config\Matrix;

/**
 * Interceptor class for @see \Ced\CsProduct\Block\ConfigurableProduct\Product\Edit\Tab\Variations\Config\Matrix
 */
class Interceptor extends \Ced\CsProduct\Block\ConfigurableProduct\Product\Edit\Tab\Variations\Config\Matrix implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Helper\Image $image, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, \Magento\Catalog\Model\Locator\LocatorInterface $locator, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $configurableType, $stockRegistry, $variationMatrix, $productRepository, $image, $localeCurrency, $locator, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
