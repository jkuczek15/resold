<?php
namespace Ced\CsMessaging\Block\Adminhtml\Message\Edit\Tabs;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Adminhtml\Message\Edit\Tabs
 */
class Interceptor extends \Ced\CsMessaging\Block\Adminhtml\Message\Edit\Tabs implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\ObjectManagerInterface $objectManager, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $authSession, $objectManager, $messagingFactory, $data);
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
