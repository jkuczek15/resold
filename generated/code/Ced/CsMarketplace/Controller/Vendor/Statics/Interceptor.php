<?php
namespace Ced\CsMarketplace\Controller\Vendor\Statics;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vendor\Statics
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vendor\Statics implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface, \Magento\Framework\Locale\Currency $localeCurrency, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $resultJsonFactory, $priceCurrencyInterface, $localeCurrency, $storeManager);
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
