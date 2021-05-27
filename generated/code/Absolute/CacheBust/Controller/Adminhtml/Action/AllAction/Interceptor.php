<?php
namespace Absolute\CacheBust\Controller\Adminhtml\Action\AllAction;

/**
 * Interceptor class for @see \Absolute\CacheBust\Controller\Adminhtml\Action\AllAction
 */
class Interceptor extends \Absolute\CacheBust\Controller\Adminhtml\Action\AllAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Absolute\CacheBust\Model\CacheBust $cacheBust)
    {
        $this->___init();
        parent::__construct($context, $cacheBust);
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
