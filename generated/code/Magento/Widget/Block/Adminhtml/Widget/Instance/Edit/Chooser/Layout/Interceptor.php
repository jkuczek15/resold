<?php
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout;

/**
 * Interceptor class for @see \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout
 */
class Interceptor extends \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Layout implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Framework\View\Layout\PageType\Config $config, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $config, $data);
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
