<?php
namespace Magezon\Newsletter\Controller\Subscriber\Email;

/**
 * Interceptor class for @see \Magezon\Newsletter\Controller\Subscriber\Email
 */
class Interceptor extends \Magezon\Newsletter\Controller\Subscriber\Email implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory)
    {
        $this->___init();
        parent::__construct($context, $subscriberFactory);
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
