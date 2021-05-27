<?php
namespace Magento\AdminNotification\Block\Grid\Renderer\Severity;

/**
 * Interceptor class for @see \Magento\AdminNotification\Block\Grid\Renderer\Severity
 */
class Interceptor extends \Magento\AdminNotification\Block\Grid\Renderer\Severity implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\AdminNotification\Model\Inbox $notice, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $notice, $data);
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
