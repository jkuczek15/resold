<?php
namespace Ced\CsMarketplace\Controller\Vreports\Filtervorders;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vreports\Filtervorders
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vreports\Filtervorders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Ced\CsMarketplace\Model\Session $MarketplaceSession)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $resultJsonFactory, $MarketplaceSession);
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
