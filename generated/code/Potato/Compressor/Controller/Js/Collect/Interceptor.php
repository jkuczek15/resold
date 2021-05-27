<?php
namespace Potato\Compressor\Controller\Js\Collect;

/**
 * Interceptor class for @see \Potato\Compressor\Controller\Js\Collect
 */
class Interceptor extends \Potato\Compressor\Controller\Js\Collect implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \Potato\Compressor\Model\RequireJsManager $requireJsManager)
    {
        $this->___init();
        parent::__construct($context, $jsonFactory, $requireJsManager);
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
