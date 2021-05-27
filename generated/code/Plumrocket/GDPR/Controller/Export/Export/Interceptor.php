<?php
namespace Plumrocket\GDPR\Controller\Export\Export;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Export\Export
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Export\Export implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Plumrocket\GDPR\Helper\Data $helper, \Plumrocket\GDPR\Model\EmailSender $emailSender, \Plumrocket\GDPR\Helper\CustomerData $customerData, \Plumrocket\GDPR\Model\Account\Processor $processor, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Model\AuthenticationInterface $authentication, \Magento\Customer\Model\Session $session, \Plumrocket\GDPR\Model\ExportLogFactory $logFactory, \Plumrocket\GDPR\Model\ResourceModel\ExportLogFactory $logResourceFactory, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime)
    {
        $this->___init();
        parent::__construct($context, $formKeyValidator, $helper, $emailSender, $customerData, $processor, $customerRepository, $authentication, $session, $logFactory, $logResourceFactory, $remoteAddress, $dateTime);
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
