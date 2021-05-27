<?php
namespace Infortis\Cgen\Controller\Dynamic\Assets;

/**
 * Interceptor class for @see \Infortis\Cgen\Controller\Dynamic\Assets
 */
class Interceptor extends \Infortis\Cgen\Controller\Dynamic\Assets implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Layout $frameworkViewLayout, \Infortis\Cgen\Helper\Definitions $configHelper)
    {
        $this->___init();
        parent::__construct($context, $frameworkViewLayout, $configHelper);
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
