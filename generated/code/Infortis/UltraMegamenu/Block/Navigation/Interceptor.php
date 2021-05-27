<?php
namespace Infortis\UltraMegamenu\Block\Navigation;

/**
 * Interceptor class for @see \Infortis\UltraMegamenu\Block\Navigation
 */
class Interceptor extends \Infortis\UltraMegamenu\Block\Navigation implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Magento\Framework\App\Http\Context $httpContext, \Magento\Catalog\Helper\Category $catalogCategory, \Magento\Framework\Registry $registry, \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState, \Magento\Framework\Registry $frameworkRegistry, \Magento\Customer\Model\Session $modelSession, \Infortis\UltraMegamenu\Helper\Data $helperData, \Magento\Catalog\Model\ResourceModel\Category\Flat $resourceModelCategoryFlat, \Magento\Catalog\Model\Indexer\Category\Flat\State $flatCategoryState, \Magento\Cms\Helper\Page $cmsHelperPage, \Magento\Cms\Model\Template\FilterProvider $filterProvider, \Magento\Catalog\Model\LayerFactory $catalogModelLayerFactory, \Magento\Catalog\Model\Layer\CategoryFactory $categoryLayerFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $categoryFactory, $productCollectionFactory, $layerResolver, $httpContext, $catalogCategory, $registry, $flatState, $frameworkRegistry, $modelSession, $helperData, $resourceModelCategoryFlat, $flatCategoryState, $cmsHelperPage, $filterProvider, $catalogModelLayerFactory, $categoryLayerFactory, $data);
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
