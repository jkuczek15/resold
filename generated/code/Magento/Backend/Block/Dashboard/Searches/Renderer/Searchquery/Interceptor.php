<?php
namespace Magento\Backend\Block\Dashboard\Searches\Renderer\Searchquery;

/**
 * Interceptor class for @see \Magento\Backend\Block\Dashboard\Searches\Renderer\Searchquery
 */
class Interceptor extends \Magento\Backend\Block\Dashboard\Searches\Renderer\Searchquery implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Stdlib\StringUtils $stringHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $stringHelper, $data);
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
