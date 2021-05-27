<?php
namespace Magento\Multishipping\Block\Checkout\Billing;

/**
 * Interceptor class for @see \Magento\Multishipping\Block\Checkout\Billing
 */
class Interceptor extends \Magento\Multishipping\Block\Checkout\Billing implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Payment\Helper\Data $paymentHelper, \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory, \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Payment\Model\Method\SpecificationInterface $paymentSpecification, array $data = [], array $additionalChecks = [])
    {
        $this->___init();
        parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $multishipping, $checkoutSession, $paymentSpecification, $data, $additionalChecks);
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
