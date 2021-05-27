<?php
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

/**
 * Interceptor class for @see \Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link
 */
class Interceptor extends \Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Json\Helper\Data $jsonHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonHelper, $data);
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
