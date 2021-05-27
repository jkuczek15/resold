<?php
namespace Ced\CsProduct\Controller\Vproducts\Save;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Vproducts\Save
 */
class Interceptor extends \Ced\CsProduct\Controller\Vproducts\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager, \Magento\Catalog\Model\Product\Copier $productCopier, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository)
    {
        $this->___init();
        parent::__construct($context, $productBuilder, $customerSession, $productTypeManager, $productCopier, $resultPageFactory, $urlFactory, $resultForwardFactory, $moduleManager, $productRepository);
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
