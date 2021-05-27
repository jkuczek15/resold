<?php
namespace Infortis\Dataporter\Block\System\Config\Form\Field\Configimpex;

/**
 * Interceptor class for @see \Infortis\Dataporter\Block\System\Config\Form\Field\Configimpex
 */
class Interceptor extends \Infortis\Dataporter\Block\System\Config\Form\Field\Configimpex implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\View\LayoutFactory $viewLayoutFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $viewLayoutFactory, $data);
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
