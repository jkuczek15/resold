<?php
namespace Magento\Catalog\Model\Config;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Config
 */
class Interceptor extends \Magento\Catalog\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\CacheInterface $cache, \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory, \Magento\Eav\Model\ResourceModel\Entity\Type\CollectionFactory $entityTypeCollectionFactory, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\Validator\UniversalFactory $universalFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Catalog\Model\ResourceModel\ConfigFactory $configFactory, \Magento\Catalog\Model\Product\TypeFactory $productTypeFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $groupCollectionFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setCollectionFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Eav\Model\Config $eavConfig, \Magento\Framework\Serialize\SerializerInterface $serializer = null)
    {
        $this->___init();
        parent::__construct($cache, $entityTypeFactory, $entityTypeCollectionFactory, $cacheState, $universalFactory, $scopeConfig, $configFactory, $productTypeFactory, $groupCollectionFactory, $setCollectionFactory, $storeManager, $eavConfig, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeUsedForSortByArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributeUsedForSortByArray');
        if (!$pluginInfo) {
            return parent::getAttributeUsedForSortByArray();
        } else {
            return $this->___callPlugins('getAttributeUsedForSortByArray', func_get_args(), $pluginInfo);
        }
    }
}
