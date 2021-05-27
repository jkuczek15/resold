<?php
namespace Magento\Sales\Block\Adminhtml\Reorder\Renderer\Action;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Reorder\Renderer\Action
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Reorder\Renderer\Action implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Sales\Helper\Reorder $salesReorder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $salesReorder, $data);
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
