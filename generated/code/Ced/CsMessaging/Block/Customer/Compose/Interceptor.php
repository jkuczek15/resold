<?php
namespace Ced\CsMessaging\Block\Customer\Compose;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Customer\Compose
 */
class Interceptor extends \Ced\CsMessaging\Block\Customer\Compose implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\Session $customerSession, \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, array $data, \Ced\CsMessaging\Helper\Data $messagingHelper, \Magento\Framework\ObjectManagerInterface $objectInterface)
    {
        $this->___init();
        parent::__construct($customerSession, $context, $backendHelper, $messagingFactory, $data, $messagingHelper, $objectInterface);
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
