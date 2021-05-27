<?php
namespace Magento\ConfigurableProduct\Block\Stockqty\Type\Configurable;

/**
 * Interceptor class for @see \Magento\ConfigurableProduct\Block\Stockqty\Type\Configurable
 */
class Interceptor extends \Magento\ConfigurableProduct\Block\Stockqty\Type\Configurable implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\CatalogInventory\Api\StockStateInterface $stockState, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $stockState, $stockRegistry, $data);
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
