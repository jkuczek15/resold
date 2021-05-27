<?php
namespace Plumrocket\GDPR\Controller\Account\Export;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Account\Export
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Account\Export implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Plumrocket\GDPR\Helper\Data $helper, \Magento\Customer\Model\Session $session)
    {
        $this->___init();
        parent::__construct($context, $helper, $session);
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
