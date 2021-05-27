<?php
namespace Plumrocket\GDPR\Controller\Consentpopups\Load;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Consentpopups\Load
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Consentpopups\Load implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Plumrocket\GDPR\Helper\Checkboxes $checkboxes, \Plumrocket\GDPR\Helper\Notifys $notifys, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Model\Session $session)
    {
        $this->___init();
        parent::__construct($context, $checkboxes, $notifys, $resultJsonFactory, $resultPageFactory, $session);
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
