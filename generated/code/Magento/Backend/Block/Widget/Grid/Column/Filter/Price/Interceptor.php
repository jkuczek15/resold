<?php
namespace Magento\Backend\Block\Widget\Grid\Column\Filter\Price;

/**
 * Interceptor class for @see \Magento\Backend\Block\Widget\Grid\Column\Filter\Price
 */
class Interceptor extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Price implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\DB\Helper $resourceHelper, \Magento\Directory\Model\Currency $currencyModel, \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resourceHelper, $currencyModel, $currencyLocator, $data);
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
