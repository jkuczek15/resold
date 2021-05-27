<?php
namespace Ced\CsProduct\Controller\Vproducts\OptionsImportGrid;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Vproducts\OptionsImportGrid
 */
class Interceptor extends \Ced\CsProduct\Controller\Vproducts\OptionsImportGrid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager)
    {
        $this->___init();
        parent::__construct($context, $productBuilder, $resultLayoutFactory, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
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
