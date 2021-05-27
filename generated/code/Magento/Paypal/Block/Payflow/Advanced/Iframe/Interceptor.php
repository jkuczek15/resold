<?php
namespace Magento\Paypal\Block\Payflow\Advanced\Iframe;

/**
 * Interceptor class for @see \Magento\Paypal\Block\Payflow\Advanced\Iframe
 */
class Interceptor extends \Magento\Paypal\Block\Payflow\Advanced\Iframe implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Paypal\Helper\Hss $hssHelper, \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory, \Magento\Framework\Module\Dir\Reader $reader, \Magento\Payment\Helper\Data $paymentData, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $orderFactory, $checkoutSession, $hssHelper, $readFactory, $reader, $paymentData, $data);
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
