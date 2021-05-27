<?php
namespace Infortis\Dataporter\Controller\Adminhtml\Cfgporter\Index\Export;

/**
 * Interceptor class for @see \Infortis\Dataporter\Controller\Adminhtml\Cfgporter\Index\Export
 */
class Interceptor extends \Infortis\Dataporter\Controller\Adminhtml\Cfgporter\Index\Export implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Infortis\Dataporter\Helper\Data $helperData, \Infortis\Dataporter\Helper\Cfgporter\Data $cfgporterData, \Psr\Log\LoggerInterface $logLoggerInterface, \Magento\Framework\View\LayoutFactory $viewLayoutFactory, \Magento\Framework\App\Config\ScopeConfigInterface $configScopeConfigInterface, \Magento\Framework\Module\Dir\Reader $dirReader, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->___init();
        parent::__construct($context, $helperData, $cfgporterData, $logLoggerInterface, $viewLayoutFactory, $configScopeConfigInterface, $dirReader, $resultPageFactory);
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
