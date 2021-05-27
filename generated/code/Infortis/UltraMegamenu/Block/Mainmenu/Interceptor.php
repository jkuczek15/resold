<?php
namespace Infortis\UltraMegamenu\Block\Mainmenu;

/**
 * Interceptor class for @see \Infortis\UltraMegamenu\Block\Mainmenu
 */
class Interceptor extends \Infortis\UltraMegamenu\Block\Mainmenu implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Infortis\UltraMegamenu\Helper\Data $helper, \Infortis\Infortis\Helper\Connector\Infortis\Base $connectorBaseTheme, \Magento\Cms\Model\BlockFactory $blockFactory, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $helper, $connectorBaseTheme, $blockFactory, $registry);
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
