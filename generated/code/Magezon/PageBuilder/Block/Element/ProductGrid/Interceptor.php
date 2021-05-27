<?php
namespace Magezon\PageBuilder\Block\Element\ProductGrid;

/**
 * Interceptor class for @see \Magezon\PageBuilder\Block\Element\ProductGrid
 */
class Interceptor extends \Magezon\PageBuilder\Block\Element\ProductGrid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\App\Http\Context $httpContext, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Framework\Url\Helper\Data $urlHelper, \Magezon\Core\Model\ProductList $productList, \Magezon\Core\Helper\Data $coreHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $httpContext, $priceCurrency, $urlHelper, $productList, $coreHelper, $data);
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
