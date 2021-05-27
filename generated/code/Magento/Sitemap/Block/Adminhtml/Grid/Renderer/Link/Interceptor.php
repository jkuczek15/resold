<?php
namespace Magento\Sitemap\Block\Adminhtml\Grid\Renderer\Link;

/**
 * Interceptor class for @see \Magento\Sitemap\Block\Adminhtml\Grid\Renderer\Link
 */
class Interceptor extends \Magento\Sitemap\Block\Adminhtml\Grid\Renderer\Link implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Sitemap\Model\SitemapFactory $sitemapFactory, \Magento\Framework\Filesystem $filesystem, array $data = [], \Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot $documentRoot = null)
    {
        $this->___init();
        parent::__construct($context, $sitemapFactory, $filesystem, $data, $documentRoot);
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
