<?php
namespace Magento\Rule\Block\Editable;

/**
 * Interceptor class for @see \Magento\Rule\Block\Editable
 */
class Interceptor extends \Magento\Rule\Block\Editable implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Framework\Translate\InlineInterface $inlineTranslate, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $inlineTranslate, $data);
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
