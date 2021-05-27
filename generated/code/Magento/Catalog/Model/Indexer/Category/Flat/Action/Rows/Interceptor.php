<?php
namespace Magento\Catalog\Model\Indexer\Category\Flat\Action\Rows;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Indexer\Category\Flat\Action\Rows
 */
class Interceptor extends \Magento\Catalog\Model\Indexer\Category\Flat\Action\Rows implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository)
    {
        $this->___init();
        parent::__construct($resource, $storeManager, $resourceHelper, $categoryRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumns');
        if (!$pluginInfo) {
            return parent::getColumns();
        } else {
            return $this->___callPlugins('getColumns', func_get_args(), $pluginInfo);
        }
    }
}
