<?php
namespace Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Send;

/**
 * Interceptor class for @see \Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Send
 */
class Interceptor extends \Experius\EmailCatcher\Controller\Adminhtml\Emailcatcher\Send implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Experius\EmailCatcher\Model\Mail $mail)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $mail);
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
