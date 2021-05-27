<?php
namespace Ced\CsMarketplace\Block\Vorders\View\Info;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Vorders\View\Info
 */
class Interceptor extends \Ced\CsMarketplace\Block\Vorders\View\Info implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Payment\Helper\Data $paymentHelper, \Magento\Sales\Model\Order\Address\Renderer $addressRenderer, \Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $objectManager, $data);
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
