<?php
namespace Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action;

/**
 * Interceptor class for @see \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action
 */
class Interceptor extends \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $actionUrlBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $actionUrlBuilder, $data);
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
