<?php
namespace Mageplaza\Gdpr\Controller\Account\Delete;

/**
 * Interceptor class for @see \Mageplaza\Gdpr\Controller\Account\Delete
 */
class Interceptor extends \Mageplaza\Gdpr\Controller\Account\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Registry $registry, \Psr\Log\LoggerInterface $logger, \Mageplaza\Gdpr\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $customerRepository, $customerSession, $registry, $logger, $helper);
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
