<?php
namespace Magento\Catalog\Helper\Category;

/**
 * Interceptor class for @see \Magento\Catalog\Helper\Category
 */
class Interceptor extends \Magento\Catalog\Helper\Category implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\CollectionFactory $dataCollectionFactory, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository)
    {
        $this->___init();
        parent::__construct($context, $categoryFactory, $storeManager, $dataCollectionFactory, $categoryRepository);
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
