<?php
namespace Plumrocket\GDPR\Controller\Delete\Delete;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Delete\Delete
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Delete\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime, \Magento\Customer\Model\Session $session, \Plumrocket\GDPR\Helper\Data $helper, \Plumrocket\GDPR\Model\EmailSender $emailSender, \Plumrocket\GDPR\Helper\CustomerData $customerData, \Plumrocket\GDPR\Model\RemovalRequestsFactory $removalFactory, \Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory $removalResourceFactory, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $remoteAddress, $dateTime, $session, $helper, $emailSender, $customerData, $removalFactory, $removalResourceFactory, $jsonHelper, $resultJsonFactory, $storeManager);
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
