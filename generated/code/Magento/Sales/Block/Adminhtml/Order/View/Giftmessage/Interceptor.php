<?php
namespace Magento\Sales\Block\Adminhtml\Order\View\Giftmessage;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\View\Giftmessage
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\View\Giftmessage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\GiftMessage\Model\MessageFactory $messageFactory, \Magento\Framework\Registry $registry, \Magento\GiftMessage\Helper\Message $messageHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $messageFactory, $registry, $messageHelper, $data);
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
