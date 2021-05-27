<?php
namespace Plumrocket\GDPR\Controller\Adminhtml\Revision\History\View;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Adminhtml\Revision\History\View
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Adminhtml\Revision\History\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Plumrocket\GDPR\Model\Revision\HistoryFactory $historyFactory, \Plumrocket\GDPR\Model\ResourceModel\Revision\History $historyResource)
    {
        $this->___init();
        parent::__construct($context, $resultLayoutFactory, $historyFactory, $historyResource);
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
