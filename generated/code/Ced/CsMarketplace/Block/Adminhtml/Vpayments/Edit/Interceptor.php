<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\HTTP\Header $header, \Ced\CsMarketplace\Helper\Acl $acl, \Ced\CsMarketplace\Model\Vendor $vendor, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $header, $acl, $vendor, $data);
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
