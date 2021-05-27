<?php
namespace Magento\Catalog\Block\Rss\Product\NewProducts;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Rss\Product\NewProducts
 */
class Interceptor extends \Magento\Catalog\Block\Rss\Product\NewProducts implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Helper\Image $imageHelper, \Magento\Catalog\Model\Rss\Product\NewProducts $rssModel, \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $imageHelper, $rssModel, $rssUrlBuilder, $data);
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
