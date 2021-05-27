<?php
namespace Magento\Catalog\Block\Adminhtml\Rss\NotifyStock;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Rss\NotifyStock
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Rss\NotifyStock implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Catalog\Model\Rss\Product\NotifyStock $rssModel, \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $rssModel, $rssUrlBuilder, $data);
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
