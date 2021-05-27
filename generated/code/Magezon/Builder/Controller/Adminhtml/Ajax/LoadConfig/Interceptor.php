<?php
namespace Magezon\Builder\Controller\Adminhtml\Ajax\LoadConfig;

/**
 * Interceptor class for @see \Magezon\Builder\Controller\Adminhtml\Ajax\LoadConfig
 */
class Interceptor extends \Magezon\Builder\Controller\Adminhtml\Ajax\LoadConfig implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\Stdlib\ArrayManager $arrayManager, \Magezon\Builder\Model\CompositeConfigProvider $configProvider, \Magezon\Builder\Model\CacheManager $cacheManager)
    {
        $this->___init();
        parent::__construct($context, $layoutFactory, $arrayManager, $configProvider, $cacheManager);
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
