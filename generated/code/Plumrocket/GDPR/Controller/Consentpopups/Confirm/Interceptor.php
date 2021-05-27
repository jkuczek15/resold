<?php
namespace Plumrocket\GDPR\Controller\Consentpopups\Confirm;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Consentpopups\Confirm
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Consentpopups\Confirm implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Plumrocket\GDPR\Model\ConsentsLogFactory $consentsLogFactory, \Plumrocket\GDPR\Model\ResourceModel\ConsentsLogFactory $consentsLogResourceFactory, \Magento\Customer\Model\Session $session, \Magento\Framework\Filter\FilterManager $filterManager, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress, \Magento\Framework\Stdlib\DateTime\DateTime $dateTime)
    {
        $this->___init();
        parent::__construct($context, $consentsLogFactory, $consentsLogResourceFactory, $session, $filterManager, $resultJsonFactory, $jsonHelper, $storeManager, $remoteAddress, $dateTime);
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
