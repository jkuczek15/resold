<?php
namespace Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Cleanup;

/**
 * Interceptor class for @see \Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Cleanup
 */
class Interceptor extends \Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Cleanup implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Experius\EmailCatcher\Cron\Clean $clean)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $clean);
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
