<?php
namespace Apptrian\FacebookPixel\Block\Adminhtml\About;

/**
 * Interceptor class for @see \Apptrian\FacebookPixel\Block\Adminhtml\About
 */
class Interceptor extends \Apptrian\FacebookPixel\Block\Adminhtml\About implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Apptrian\FacebookPixel\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($helper);
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
