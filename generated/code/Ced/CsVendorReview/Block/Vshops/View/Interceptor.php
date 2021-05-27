<?php
namespace Ced\CsVendorReview\Block\Vshops\View;

/**
 * Interceptor class for @see \Ced\CsVendorReview\Block\Vshops\View
 */
class Interceptor extends \Ced\CsVendorReview\Block\Vshops\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Registry $registry, \Magento\Customer\Model\Session $customerSession, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $objectManager, $registry, $customerSession, $data);
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
