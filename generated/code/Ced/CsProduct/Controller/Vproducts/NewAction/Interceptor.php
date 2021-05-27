<?php
namespace Ced\CsProduct\Controller\Vproducts\NewAction;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Vproducts\NewAction
 */
class Interceptor extends \Ced\CsProduct\Controller\Vproducts\NewAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Store\Model\StoreManagerInterface $_storeManager, \Magento\Framework\Module\Manager $moduleManager, \Ced\CsProduct\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $productBuilder, $customerSession, $stockFilter, $resultPageFactory, $urlFactory, $resultForwardFactory, $_storeManager, $moduleManager, $helper);
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
