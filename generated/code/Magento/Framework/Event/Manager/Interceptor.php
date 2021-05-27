<?php
namespace Magento\Framework\Event\Manager;

/**
 * Interceptor class for @see \Magento\Framework\Event\Manager
 */
class Interceptor extends \Magento\Framework\Event\Manager implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Event\InvokerInterface $invoker, \Magento\Framework\Event\ConfigInterface $eventConfig)
    {
        $this->___init();
        parent::__construct($invoker, $eventConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, array $data = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($eventName, $data);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
