<?php
namespace Ced\CsMarketplace\Controller\Vpayments\View;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vpayments\View
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vpayments\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Ced\CsMarketplace\Model\Vpayment $vpayment, \Ced\CsMarketplace\Helper\Payment $payment, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $vpayment, $payment, $registry);
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
