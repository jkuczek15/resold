<?php
namespace Ced\CsMarketplace\Block\Vpayments\View;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Vpayments\View
 */
class Interceptor extends \Ced\CsMarketplace\Block\Vpayments\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Locale\Currency $localeCurrency, \Ced\CsMarketplace\Helper\Acl $acl)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $objectManager, $urlFactory, $localeCurrency, $acl);
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
