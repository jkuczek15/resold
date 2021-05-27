<?php
namespace Magento\CatalogSearch\Model\Indexer\Fulltext;

/**
 * Interceptor class for @see \Magento\CatalogSearch\Model\Indexer\Fulltext
 */
class Interceptor extends \Magento\CatalogSearch\Model\Indexer\Fulltext implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\CatalogSearch\Model\Indexer\Fulltext\Action\FullFactory $fullActionFactory, \Magento\CatalogSearch\Model\Indexer\IndexerHandlerFactory $indexerHandlerFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Search\Request\DimensionFactory $dimensionFactory, \Magento\CatalogSearch\Model\ResourceModel\Fulltext $fulltextResource, \Magento\Framework\Search\Request\Config $searchRequestConfig, array $data, \Magento\CatalogSearch\Model\Indexer\IndexSwitcherInterface $indexSwitcher = null, \Magento\CatalogSearch\Model\Indexer\Scope\State $indexScopeState = null)
    {
        $this->___init();
        parent::__construct($fullActionFactory, $indexerHandlerFactory, $storeManager, $dimensionFactory, $fulltextResource, $searchRequestConfig, $data, $indexSwitcher, $indexScopeState);
    }

    /**
     * {@inheritdoc}
     */
    public function executeFull()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'executeFull');
        if (!$pluginInfo) {
            return parent::executeFull();
        } else {
            return $this->___callPlugins('executeFull', func_get_args(), $pluginInfo);
        }
    }
}
