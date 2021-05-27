<?php
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

/**
 * Interceptor class for @see \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config
 */
class Interceptor extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $configurableType, $data);
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
