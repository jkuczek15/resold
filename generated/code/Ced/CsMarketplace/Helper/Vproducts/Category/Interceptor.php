<?php
namespace Ced\CsMarketplace\Helper\Vproducts\Category;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Helper\Vproducts\Category
 */
class Interceptor extends \Ced\CsMarketplace\Helper\Vproducts\Category implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\CollectionFactory $dataCollectionFactory, \Magento\Catalog\Model\CategoryRepository $categoryRepository, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterface)
    {
        $this->___init();
        parent::__construct($context, $objectManager, $categoryFactory, $storeManager, $dataCollectionFactory, $categoryRepository, $categoryRepositoryInterface);
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
