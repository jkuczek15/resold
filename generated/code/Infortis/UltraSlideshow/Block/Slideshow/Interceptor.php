<?php
namespace Infortis\UltraSlideshow\Block\Slideshow;

/**
 * Interceptor class for @see \Infortis\UltraSlideshow\Block\Slideshow
 */
class Interceptor extends \Infortis\UltraSlideshow\Block\Slideshow implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Infortis\UltraSlideshow\Helper\Data $helperData, \Magento\Customer\Model\Session $modelSession, \Magento\Framework\View\LayoutFactory $viewLayoutFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperData, $modelSession, $viewLayoutFactory, $data);
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
