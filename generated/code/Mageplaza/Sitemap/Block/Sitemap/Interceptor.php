<?php
namespace Mageplaza\Sitemap\Block\Sitemap;

/**
 * Interceptor class for @see \Mageplaza\Sitemap\Block\Sitemap
 */
class Interceptor extends \Mageplaza\Sitemap\Block\Sitemap implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Helper\Category $categoryHelper, \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState, \Magento\Theme\Block\Html\Topmenu $topMenu, \Magento\Catalog\Model\ResourceModel\Category\Collection $collection, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection, \Magento\Catalog\Model\CategoryRepository $categoryRepository, \Mageplaza\Sitemap\Helper\Data $helper, \Magento\CatalogInventory\Helper\Stock $stockFilter, \Magento\Catalog\Model\Product\Visibility $productVisibility, \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection, \Magento\Cms\Model\ResourceModel\Page\Collection $pageCollection)
    {
        $this->___init();
        parent::__construct($context, $categoryHelper, $categoryFlatState, $topMenu, $collection, $categoryCollection, $categoryRepository, $helper, $stockFilter, $productVisibility, $productCollection, $pageCollection);
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
