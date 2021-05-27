<?php
namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\Button;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\Button
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\Button implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $data);
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
