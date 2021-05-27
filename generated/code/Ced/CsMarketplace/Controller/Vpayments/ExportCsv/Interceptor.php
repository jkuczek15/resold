<?php
namespace Ced\CsMarketplace\Controller\Vpayments\ExportCsv;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vpayments\ExportCsv
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vpayments\ExportCsv implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Ced\CsMarketplace\Helper\Payment $payment)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $fileFactory, $payment);
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
