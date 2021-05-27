<?php
namespace Magento\Catalog\Block\Rss\Category;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Rss\Category
 */
class Interceptor extends \Magento\Catalog\Block\Rss\Category implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Model\Rss\Category $rssModel, \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder, \Magento\Catalog\Helper\Image $imageHelper, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $categoryFactory, $rssModel, $rssUrlBuilder, $imageHelper, $customerSession, $categoryRepository, $data);
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
