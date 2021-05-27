<?php
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Group;

/**
 * Interceptor class for @see \Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Group
 */
class Interceptor extends \Magento\Paypal\Block\Adminhtml\System\Config\Fieldset\Group implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\View\Helper\Js $jsHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $authSession, $jsHelper, $data);
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
