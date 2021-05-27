<?php
namespace Magezon\Builder\Controller\Adminhtml\Ajax\LoadStyles;

/**
 * Interceptor class for @see \Magezon\Builder\Controller\Adminhtml\Ajax\LoadStyles
 */
class Interceptor extends \Magezon\Builder\Controller\Adminhtml\Ajax\LoadStyles implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magezon\Core\Helper\Data $coreHelper, \Magezon\Builder\Helper\Data $builderHelper)
    {
        $this->___init();
        parent::__construct($context, $layoutFactory, $coreHelper, $builderHelper);
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
