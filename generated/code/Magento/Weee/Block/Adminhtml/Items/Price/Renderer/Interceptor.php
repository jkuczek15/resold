<?php
namespace Magento\Weee\Block\Adminhtml\Items\Price\Renderer;

/**
 * Interceptor class for @see \Magento\Weee\Block\Adminhtml\Items\Price\Renderer
 */
class Interceptor extends \Magento\Weee\Block\Adminhtml\Items\Price\Renderer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $defaultColumnRenderer, \Magento\Tax\Helper\Data $taxHelper, \Magento\Weee\Block\Item\Price\Renderer $itemPriceRenderer, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $defaultColumnRenderer, $taxHelper, $itemPriceRenderer, $data);
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
