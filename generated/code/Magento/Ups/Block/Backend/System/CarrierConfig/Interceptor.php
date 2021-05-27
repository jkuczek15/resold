<?php
namespace Magento\Ups\Block\Backend\System\CarrierConfig;

/**
 * Interceptor class for @see \Magento\Ups\Block\Backend\System\CarrierConfig
 */
class Interceptor extends \Magento\Ups\Block\Backend\System\CarrierConfig implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Ups\Helper\Config $carrierConfig, \Magento\Store\Model\Website $websiteModel, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $carrierConfig, $websiteModel, $data);
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
