<?php
namespace Experius\EmailCatcher\Controller\Adminhtml\Preview\Index;

/**
 * Interceptor class for @see \Experius\EmailCatcher\Controller\Adminhtml\Preview\Index
 */
class Interceptor extends \Experius\EmailCatcher\Controller\Adminhtml\Preview\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Experius\EmailCatcher\Model\EmailcatcherFactory $emailCatcher)
    {
        $this->___init();
        parent::__construct($context, $resultRawFactory, $emailCatcher);
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
