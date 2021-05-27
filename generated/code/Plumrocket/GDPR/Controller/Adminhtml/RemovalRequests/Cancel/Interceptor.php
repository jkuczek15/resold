<?php
namespace Plumrocket\GDPR\Controller\Adminhtml\RemovalRequests\Cancel;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Adminhtml\RemovalRequests\Cancel
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Adminhtml\RemovalRequests\Cancel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Plumrocket\GDPR\Model\RemovalRequestsFactory $removalFactory, \Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory $removalResourceFactory, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, \Magento\Backend\Model\Auth\Session $authSession)
    {
        $this->___init();
        parent::__construct($context, $removalFactory, $removalResourceFactory, $dateTime, $authSession);
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
