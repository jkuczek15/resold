<?php
namespace Ced\CsMessaging\Block\Customer\Vendorcustomer\Sent;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Customer\Vendorcustomer\Sent
 */
class Interceptor extends \Ced\CsMessaging\Block\Customer\Vendorcustomer\Sent implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\Session $customerSession, \Magento\Backend\Block\Template\Context $context, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Ced\CsMessaging\Helper\Data $messagingHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($customerSession, $context, $messagingFactory, $messagingHelper, $data);
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
