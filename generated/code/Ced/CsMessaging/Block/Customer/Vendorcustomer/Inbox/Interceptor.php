<?php
namespace Ced\CsMessaging\Block\Customer\Vendorcustomer\Inbox;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Customer\Vendorcustomer\Inbox
 */
class Interceptor extends \Ced\CsMessaging\Block\Customer\Vendorcustomer\Inbox implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\Session $customerSession, \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Customer\Model\CustomerFactory $customerFactory, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Ced\CsMessaging\Helper\Data $mesagingHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($customerSession, $context, $backendHelper, $customerFactory, $messagingFactory, $mesagingHelper, $data);
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
