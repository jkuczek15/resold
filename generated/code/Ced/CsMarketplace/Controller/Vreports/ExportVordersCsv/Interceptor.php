<?php
namespace Ced\CsMarketplace\Controller\Vreports\ExportVordersCsv;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vreports\ExportVordersCsv
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vreports\ExportVordersCsv implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Ced\CsMarketplace\Controller\Vreports\Vorders $reportorder)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $fileFactory, $reportorder);
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
