<?php
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor\Viewsendmessage;

/**
 * Interceptor class for @see \Ced\CsMessaging\Controller\Adminhtml\Vendor\Viewsendmessage
 */
class Interceptor extends \Ced\CsMessaging\Controller\Adminhtml\Vendor\Viewsendmessage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $messagingFactory);
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
