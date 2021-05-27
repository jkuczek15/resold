<?php
namespace Magezon\Builder\Controller\Adminhtml\Ajax\LoadElement;

/**
 * Interceptor class for @see \Magezon\Builder\Controller\Adminhtml\Ajax\LoadElement
 */
class Interceptor extends \Magezon\Builder\Controller\Adminhtml\Ajax\LoadElement implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Store\Model\App\Emulation $appEmulation, \Magento\Framework\App\State $appState, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Registry $registry, \Magezon\Builder\Data\Elements $elements, \Magezon\Core\Helper\Data $coreHelper)
    {
        $this->___init();
        parent::__construct($context, $resultRawFactory, $appEmulation, $appState, $storeManager, $registry, $elements, $coreHelper);
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
