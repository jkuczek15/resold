<?php
namespace Magento\CurrencySymbol\Block\Adminhtml\System\Currency\Rate\Matrix;

/**
 * Interceptor class for @see \Magento\CurrencySymbol\Block\Adminhtml\System\Currency\Rate\Matrix
 */
class Interceptor extends \Magento\CurrencySymbol\Block\Adminhtml\System\Currency\Rate\Matrix implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Directory\Model\CurrencyFactory $dirCurrencyFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $dirCurrencyFactory, $data);
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
