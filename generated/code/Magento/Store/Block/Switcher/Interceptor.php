<?php
namespace Magento\Store\Block\Switcher;

/**
 * Interceptor class for @see \Magento\Store\Block\Switcher
 */
class Interceptor extends \Magento\Store\Block\Switcher implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $postDataHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetStorePostData(\Magento\Store\Model\Store $store, $data = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTargetStorePostData');
        if (!$pluginInfo) {
            return parent::getTargetStorePostData($store, $data);
        } else {
            return $this->___callPlugins('getTargetStorePostData', func_get_args(), $pluginInfo);
        }
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
