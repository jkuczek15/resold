<?php
namespace Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Product\Edit\Tab\Googleoptimizer;

/**
 * Interceptor class for @see \Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Product\Edit\Tab\Googleoptimizer
 */
class Interceptor extends \Magento\GoogleOptimizer\Block\Adminhtml\Catalog\Product\Edit\Tab\Googleoptimizer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\GoogleOptimizer\Helper\Data $helperData, \Magento\Framework\Registry $registry, \Magento\GoogleOptimizer\Helper\Code $codeHelper, \Magento\GoogleOptimizer\Helper\Form $formHelper, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperData, $registry, $codeHelper, $formHelper, $formFactory, $data);
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
