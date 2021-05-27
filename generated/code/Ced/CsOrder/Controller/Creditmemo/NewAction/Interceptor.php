<?php
namespace Ced\CsOrder\Controller\Creditmemo\NewAction;

/**
 * Interceptor class for @see \Ced\CsOrder\Controller\Creditmemo\NewAction
 */
class Interceptor extends \Ced\CsOrder\Controller\Creditmemo\NewAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader, \Magento\Sales\Model\Service\InvoiceService $invoiceService, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $creditmemoLoader, $invoiceService, $registry);
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
