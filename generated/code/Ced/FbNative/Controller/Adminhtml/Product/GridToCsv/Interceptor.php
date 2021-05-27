<?php
namespace Ced\FbNative\Controller\Adminhtml\Product\GridToCsv;

/**
 * Interceptor class for @see \Ced\FbNative\Controller\Adminhtml\Product\GridToCsv
 */
class Interceptor extends \Ced\FbNative\Controller\Adminhtml\Product\GridToCsv implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem\Io\File $fileIo, \Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Store\Model\StoreManager $storeManager, \Ced\FbNative\Helper\Data $dataHelper, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($directoryList, $fileIo, $context, $productBuilder, $productPriceIndexerProcessor, $filter, $storeManager, $dataHelper, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
