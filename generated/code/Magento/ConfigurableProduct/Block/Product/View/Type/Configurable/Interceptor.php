<?php
namespace Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

/**
 * Interceptor class for @see \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
 */
class Interceptor extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\ConfigurableProduct\Helper\Data $helper, \Magento\Catalog\Helper\Product $catalogProduct, \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData, array $data = [], \Magento\Framework\Locale\Format $localeFormat = null, \Magento\Customer\Model\Session $customerSession = null)
    {
        $this->___init();
        parent::__construct($context, $arrayUtils, $jsonEncoder, $helper, $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $data, $localeFormat, $customerSession);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
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
