<?php
namespace Magento\ConfigurableProduct\Block\Product\Configurable\AssociatedSelector\Renderer\Id;

/**
 * Interceptor class for @see \Magento\ConfigurableProduct\Block\Product\Configurable\AssociatedSelector\Renderer\Id
 */
class Interceptor extends \Magento\ConfigurableProduct\Block\Product\Configurable\AssociatedSelector\Renderer\Id implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Catalog\Helper\Product $productHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $productHelper, $data);
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
