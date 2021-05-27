<?php
namespace Magezon\Builder\Controller\Adminhtml\Ajax\ItemList;

/**
 * Interceptor class for @see \Magezon\Builder\Controller\Adminhtml\Ajax\ItemList
 */
class Interceptor extends \Magezon\Builder\Controller\Adminhtml\Ajax\ItemList implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magezon\Builder\Data\SourcesFactory $sourcesFactory)
    {
        $this->___init();
        parent::__construct($context, $sourcesFactory);
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
