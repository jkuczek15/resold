<?php
namespace ADM\QuickDevBar\Block\Tab\Content\Layout;

/**
 * Interceptor class for @see \ADM\QuickDevBar\Block\Tab\Content\Layout
 */
class Interceptor extends \ADM\QuickDevBar\Block\Tab\Content\Layout implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \ADM\QuickDevBar\Helper\Data $qdbHelper, \Magento\Framework\Json\Helper\Data $jsonHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $qdbHelper, $jsonHelper, $data);
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
