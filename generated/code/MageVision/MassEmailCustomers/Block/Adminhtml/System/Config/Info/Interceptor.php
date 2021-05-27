<?php
namespace MageVision\MassEmailCustomers\Block\Adminhtml\System\Config\Info;

/**
 * Interceptor class for @see \MageVision\MassEmailCustomers\Block\Adminhtml\System\Config\Info
 */
class Interceptor extends \MageVision\MassEmailCustomers\Block\Adminhtml\System\Config\Info implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \MageVision\MassEmailCustomers\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $helper);
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
