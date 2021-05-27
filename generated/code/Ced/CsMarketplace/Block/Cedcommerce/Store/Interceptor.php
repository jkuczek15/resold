<?php
namespace Ced\CsMarketplace\Block\Cedcommerce\Store;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Cedcommerce\Store
 */
class Interceptor extends \Ced\CsMarketplace\Block\Cedcommerce\Store implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\ObjectManagerInterface $objectInterface, \Magento\Framework\View\Helper\Js $jsHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $authSession, $objectInterface, $jsHelper, $data);
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
