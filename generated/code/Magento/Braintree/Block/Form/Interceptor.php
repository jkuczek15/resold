<?php
namespace Magento\Braintree\Block\Form;

/**
 * Interceptor class for @see \Magento\Braintree\Block\Form
 */
class Interceptor extends \Magento\Braintree\Block\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Payment\Model\Config $paymentConfig, \Magento\Backend\Model\Session\Quote $sessionQuote, \Magento\Braintree\Gateway\Config\Config $gatewayConfig, \Magento\Braintree\Model\Adminhtml\Source\CcType $ccType, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $paymentConfig, $sessionQuote, $gatewayConfig, $ccType, $data);
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
