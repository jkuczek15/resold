<?php
namespace Apptrian\FacebookPixel\Block\Code;

/**
 * Interceptor class for @see \Apptrian\FacebookPixel\Block\Code
 */
class Interceptor extends \Apptrian\FacebookPixel\Block\Code implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Apptrian\FacebookPixel\Helper\Data $helper, \Magento\Framework\Registry $coreRegistry, \Magento\Catalog\Helper\Data $catalogHelper, \Magento\Tax\Model\Config $taxConfig, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helper, $coreRegistry, $catalogHelper, $taxConfig, $data);
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
