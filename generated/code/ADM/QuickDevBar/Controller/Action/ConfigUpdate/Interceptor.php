<?php
namespace ADM\QuickDevBar\Controller\Action\ConfigUpdate;

/**
 * Interceptor class for @see \ADM\QuickDevBar\Controller\Action\ConfigUpdate
 */
class Interceptor extends \ADM\QuickDevBar\Controller\Action\ConfigUpdate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \ADM\QuickDevBar\Helper\Data $qdbHelper, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory)
    {
        $this->___init();
        parent::__construct($context, $qdbHelper, $resultRawFactory, $layoutFactory, $resourceConfig, $storeManager, $resultForwardFactory);
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
