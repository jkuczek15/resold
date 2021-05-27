<?php
namespace Magento\Backend\Block\Widget\Grid\Column\Filter\Select;

/**
 * Interceptor class for @see \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
 */
class Interceptor extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\DB\Helper $resourceHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resourceHelper, $data);
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
