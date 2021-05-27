<?php
namespace Ced\CsProduct\Controller\Downloadable\File\Upload;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Downloadable\File\Upload
 */
class Interceptor extends \Ced\CsProduct\Controller\Downloadable\File\Upload implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Downloadable\Model\Link $link, \Magento\Downloadable\Model\Sample $sample, \Ced\CsProduct\Helper\File $fileHelper, \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, \Magento\MediaStorage\Helper\File\Storage\Database $storageDatabase)
    {
        $this->___init();
        parent::__construct($context, $productBuilder, $customerSession, $stockFilter, $resultPageFactory, $urlFactory, $resultForwardFactory, $moduleManager, $scopeConfig, $storeManager, $link, $sample, $fileHelper, $uploaderFactory, $storageDatabase);
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
