<?php
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Carts;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit\Tab\Carts
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit\Tab\Carts implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Customer\Model\Config\Share $shareConfig, \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $shareConfig, $customerDataFactory, $dataObjectHelper, $data);
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
