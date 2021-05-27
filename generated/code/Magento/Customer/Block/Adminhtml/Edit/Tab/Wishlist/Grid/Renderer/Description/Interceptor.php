<?php
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer\Description;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer\Description
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer\Description implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $data);
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
