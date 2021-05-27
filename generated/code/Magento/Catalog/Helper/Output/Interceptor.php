<?php
namespace Magento\Catalog\Helper\Output;

/**
 * Interceptor class for @see \Magento\Catalog\Helper\Output
 */
class Interceptor extends \Magento\Catalog\Helper\Output implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Eav\Model\Config $eavConfig, \Magento\Catalog\Helper\Data $catalogData, \Magento\Framework\Escaper $escaper)
    {
        $this->___init();
        parent::__construct($context, $eavConfig, $catalogData, $escaper);
    }

    /**
     * {@inheritdoc}
     */
    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'productAttribute');
        if (!$pluginInfo) {
            return parent::productAttribute($product, $attributeHtml, $attributeName);
        } else {
            return $this->___callPlugins('productAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function categoryAttribute($category, $attributeHtml, $attributeName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'categoryAttribute');
        if (!$pluginInfo) {
            return parent::categoryAttribute($category, $attributeHtml, $attributeName);
        } else {
            return $this->___callPlugins('categoryAttribute', func_get_args(), $pluginInfo);
        }
    }
}
