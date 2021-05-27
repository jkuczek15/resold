<?php
namespace Magento\Review\Block\Adminhtml\Rss;

/**
 * Interceptor class for @see \Magento\Review\Block\Adminhtml\Rss
 */
class Interceptor extends \Magento\Review\Block\Adminhtml\Rss implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Review\Model\Rss $rssModel, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $storeManager, $rssModel, $data);
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
