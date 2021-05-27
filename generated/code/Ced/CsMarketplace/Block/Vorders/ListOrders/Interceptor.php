<?php
namespace Ced\CsMarketplace\Block\Vorders\ListOrders;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Vorders\ListOrders
 */
class Interceptor extends \Ced\CsMarketplace\Block\Vorders\ListOrders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Ced\CsMarketplace\Model\Url $vendorUrl, \Magento\Framework\UrlFactory $urlFactory, \Ced\CsMarketplace\Model\Session $mktSession, \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $vendorUrl, $urlFactory, $mktSession, $objectManager);
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
