<?php
namespace Infortis\Base\Block\Html\Header;

/**
 * Interceptor class for @see \Infortis\Base\Block\Html\Header
 */
class Interceptor extends \Infortis\Base\Block\Html\Header implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Infortis\Base\Helper\Data $helperData, \Infortis\Base\Helper\Template\Theme\Html\Header $helperTemplateHtmlHeader, \Infortis\Infortis\Helper\Connector\Infortis\UltraMegamenu $helperConnectorUltraMegamenu, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperData, $helperTemplateHtmlHeader, $helperConnectorUltraMegamenu, $data);
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
