<?php
namespace Magento\Sales\Block\Adminhtml\Transactions\Detail;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Transactions\Detail
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Transactions\Detail implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Helper\Admin $adminHelper, \Magento\Sales\Api\OrderPaymentRepositoryInterface $orderPaymentRepository, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $adminHelper, $orderPaymentRepository, $data);
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
