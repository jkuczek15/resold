<?php
namespace Ced\CsMarketplace\Controller\Vorders\Index;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vorders\Index
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vorders\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Registry $registry, \Ced\CsMarketplace\Model\Session $mktSession, \Ced\CsMarketplace\Model\Vorders $vorders)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $registry, $mktSession, $vorders);
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
