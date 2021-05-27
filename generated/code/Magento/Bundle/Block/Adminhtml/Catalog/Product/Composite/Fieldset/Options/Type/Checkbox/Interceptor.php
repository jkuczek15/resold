<?php
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\Checkbox;

/**
 * Interceptor class for @see \Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\Checkbox
 */
class Interceptor extends \Magento\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\Checkbox implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Catalog\Helper\Data $catalogData, \Magento\Framework\Registry $registry, \Magento\Framework\Stdlib\StringUtils $string, \Magento\Framework\Math\Random $mathRandom, \Magento\Checkout\Helper\Cart $cartHelper, \Magento\Tax\Helper\Data $taxData, \Magento\Framework\Pricing\Helper\Data $pricingHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $catalogData, $registry, $string, $mathRandom, $cartHelper, $taxData, $pricingHelper, $data);
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
