<?php
namespace Magento\Wishlist\Block\Rss\EmailLink;

/**
 * Interceptor class for @see \Magento\Wishlist\Block\Rss\EmailLink
 */
class Interceptor extends \Magento\Wishlist\Block\Rss\EmailLink implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Wishlist\Helper\Data $wishlistHelper, \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder, \Magento\Framework\Url\EncoderInterface $urlEncoder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $wishlistHelper, $rssUrlBuilder, $urlEncoder, $data);
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
