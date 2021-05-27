<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer\Disableshop;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer\Disableshop
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid\Renderer\Disableshop implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->___init();
        parent::__construct($objectManager, $urlBuilder);
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
