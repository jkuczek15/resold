<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Locale\Currency $localeCurrency, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $objectManager, $localeCurrency, $data);
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
