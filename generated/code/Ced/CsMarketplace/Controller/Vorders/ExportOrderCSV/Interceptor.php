<?php
namespace Ced\CsMarketplace\Controller\Vorders\ExportOrderCSV;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Vorders\ExportOrderCSV
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Vorders\ExportOrderCSV implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\App\Response\Http\FileFactory $fileFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $registry, $customerSession, $fileFactory);
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
