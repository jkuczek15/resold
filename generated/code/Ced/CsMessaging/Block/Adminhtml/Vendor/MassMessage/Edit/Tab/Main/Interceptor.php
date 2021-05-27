<?php
namespace Ced\CsMessaging\Block\Adminhtml\Vendor\MassMessage\Edit\Tab\Main;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Adminhtml\Vendor\MassMessage\Edit\Tab\Main
 */
class Interceptor extends \Ced\CsMessaging\Block\Adminhtml\Vendor\MassMessage\Edit\Tab\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\ObjectManagerInterface $objectManager, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Magento\Framework\Registry $registry, \Ced\CsMessaging\Helper\Data $messagingHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $formFactory, $objectManager, $vendorFactory, $registry, $messagingHelper, $data);
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
