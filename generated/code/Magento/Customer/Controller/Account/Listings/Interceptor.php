<?php
namespace Magento\Customer\Controller\Account\Listings;

/**
 * Interceptor class for @see \Magento\Customer\Controller\Account\Listings
 */
class Interceptor extends \Magento\Customer\Controller\Account\Listings implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Search\Model\QueryFactory $queryFactory, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\Registry $registry, \Magento\Customer\Model\Session $customerSession)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $storeManager, $queryFactory, $layerResolver, $resultJsonFactory, $layoutFactory, $registry, $customerSession);
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
