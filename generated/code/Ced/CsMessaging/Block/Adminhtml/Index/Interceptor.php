<?php
namespace Ced\CsMessaging\Block\Adminhtml\Index;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Adminhtml\Index
 */
class Interceptor extends \Ced\CsMessaging\Block\Adminhtml\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $vendorFactory, $messagingFactory, $data);
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
