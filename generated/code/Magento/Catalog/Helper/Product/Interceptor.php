<?php
namespace Magento\Catalog\Helper\Product;

/**
 * Interceptor class for @see \Magento\Catalog\Helper\Product
 */
class Interceptor extends \Magento\Catalog\Helper\Product implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\Session $catalogSession, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\Registry $coreRegistry, \Magento\Catalog\Model\Attribute\Config $attributeConfig, $reindexPriceIndexerData, $reindexProductCategoryIndexerData, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $catalogSession, $assetRepo, $coreRegistry, $attributeConfig, $reindexPriceIndexerData, $reindexProductCategoryIndexerData, $productRepository, $categoryRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function canUseCanonicalTag($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canUseCanonicalTag');
        if (!$pluginInfo) {
            return parent::canUseCanonicalTag($store);
        } else {
            return $this->___callPlugins('canUseCanonicalTag', func_get_args(), $pluginInfo);
        }
    }
}
