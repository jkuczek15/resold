<?php
namespace Magento\Framework\Event\Invoker\InvokerDefault;

/**
 * Interceptor class for @see \Magento\Framework\Event\Invoker\InvokerDefault
 */
class Interceptor extends \Magento\Framework\Event\Invoker\InvokerDefault implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Event\ObserverFactory $observerFactory, \Magento\Framework\App\State $appState)
    {
        $this->___init();
        parent::__construct($observerFactory, $appState);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(array $configuration, \Magento\Framework\Event\Observer $observer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($configuration, $observer);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
